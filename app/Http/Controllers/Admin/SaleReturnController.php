<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale; // Note: If your app uses 'Order' instead of 'Sale', rename this and related models
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\ExchangeItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = SaleReturn::with(['sale', 'customer', 'items.product', 'exchangeItems.product'])
            ->whereHas('sale', fn($q) => $q->where('user_id', $userId));

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhere('reason_notes', 'like', "%{$search}%");
            });
        }

        if ($request->has('sale_id')) {
            $query->where('sale_id', $request->input('sale_id'));
        }

        if ($request->has('return_type')) {
            $query->where('return_type', $request->input('return_type'));
        }

        if ($request->has('from_date')) {
            $query->whereDate('return_date', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('return_date', '<=', $request->input('to_date'));
        }

        $returns = $query->latest('return_date')->paginate(15);
        $sales = Sale::where('user_id', $userId)->select('id', 'order_number as sale_number', 'customer_id')->with('customer:id,name')->get();
        $customers = Customer::where('user_id', $userId)->select('id', 'name')->get();

        return view('admin.sale-returns.index', compact('returns', 'sales', 'customers'));
    }

    public function create()
    {
        $userId = Auth::id();
        // Fetch recent sales eligible for return
        $sales = Sale::where('user_id', $userId)
            ->whereIn('status', ['delivered', 'completed'])
            ->with(['items.product', 'customer'])
            ->latest()
            ->take(50)
            ->get();

        return view('admin.sale-returns.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'return_type' => 'required|string|in:full,partial',
            'reason' => 'required|string',
            'reason_notes' => 'nullable|string',
            'refund_method' => 'nullable|string|in:cash,bank,mobile_banking,store_credit',
            'refund_amount' => 'nullable|numeric|min:0',
            'exchange_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.stock_replaced' => 'required|boolean',
            'exchange_items' => 'nullable|array',
            'exchange_items.*.product_id' => 'required_with:exchange_items|exists:products,id',
            'exchange_items.*.quantity' => 'required_with:exchange_items|numeric|min:0.01',
            'exchange_items.*.unit_price' => 'required_with:exchange_items|numeric|min:0',
        ]);

        $sale = Sale::where('user_id', Auth::id())->findOrFail($validated['sale_id']);

        $existingReturn = SaleReturn::where('sale_id', $sale->id)->where('return_type', 'full')->first();
        if ($existingReturn) {
            return back()->withInput()->with('error', 'This sale has already been fully returned.');
        }

        $returnType = $validated['return_type'];
        $itemsData = $validated['items'];

        if ($returnType === 'full' && empty($itemsData)) {
            $itemsData = $sale->items()->get()->map(fn($item) => [
                'sale_item_id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => max(0, $item->quantity - $item->quantity_returned),
                'stock_replaced' => true,
            ])->toArray();
        }

        if (empty($itemsData)) {
            return back()->withInput()->with('error', 'Return items are required.');
        }

        $saleItems = $sale->items()->get()->keyBy('id');
        $validatedItems = [];
        $totalRefundAmount = 0;

        foreach ($itemsData as $item) {
            $saleItem = $saleItems->get($item['sale_item_id']);
            if (!$saleItem) {
                return back()->withInput()->with('error', "Sale item not found in this sale.");
            }

            $availableQty = $saleItem->quantity - $saleItem->quantity_returned;
            $returnQty = (float) $item['quantity'];

            if ($returnQty > $availableQty) {
                return back()->withInput()->with('error', "Return quantity for {$saleItem->product_name} exceeds available quantity ({$availableQty}).");
            }

            $product = Product::find($item['product_id']);
            $unitPrice = (float) $saleItem->unit_price;
            $itemSubtotal = $returnQty * $unitPrice;
            $totalRefundAmount += $itemSubtotal;

            $validatedItems[] = [
                'saleItem' => $saleItem,
                'product' => $product,
                'name' => $saleItem->product_name,
                'quantity' => $returnQty,
                'unit_price' => $unitPrice,
                'subtotal' => $itemSubtotal,
                'stock_replaced' => (bool) $item['stock_replaced'],
            ];
        }

        $exchangeItemsData = $request->input('exchange_items', []);
        $validatedExchangeItems = [];
        $exchangeSubtotal = 0;

        foreach ($exchangeItemsData as $item) {
            $product = Product::find($item['product_id']);
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];

            $availableStock = (float) $product->stock_quantity;
            if ($quantity > $availableStock) {
                return back()->withInput()->with('error', "Insufficient stock for {$product->name}. Available: {$availableStock}, requested: {$quantity}");
            }

            $itemSubtotal = $quantity * $unitPrice;
            $exchangeSubtotal += $itemSubtotal;

            $validatedExchangeItems[] = [
                'product' => $product,
                'name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cost_price' => (float) $product->cost_price,
                'subtotal' => $itemSubtotal,
            ];
        }

        DB::transaction(function () use ($request, $sale, $returnType, $validatedItems, $validatedExchangeItems, $totalRefundAmount, $exchangeSubtotal) {
            $returnItemsToInsert = [];
            $stockMovements = [];

            // 1. Process Returned Items (Stock In)
            foreach ($validatedItems as $v) {
                $saleItem = $v['saleItem'];
                $product = $v['product'];
                $returnQty = $v['quantity'];

                $saleItem->increment('quantity_returned', $returnQty);

                if ($v['stock_replaced']) {
                    $beforeQty = (float) $product->stock_quantity;
                    $afterQty = $beforeQty + $returnQty;

                    $product->decrement('stock_out', $returnQty);
                    $product->increment('stock_quantity', $returnQty);

                    $stockMovements[] = [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => 'in',
                        'reference_type' => 'sale_return',
                        'reference_id' => 0,
                        'quantity' => $returnQty,
                        'unit_cost' => (float) $product->cost_price,
                        'before_quantity' => $beforeQty,
                        'after_quantity' => $afterQty,
                        'notes' => "Sale return: {$returnQty} {$v['name']}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $returnItemsToInsert[] = [
                    'sale_return_id' => 0,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'name' => $v['name'],
                    'quantity' => $returnQty,
                    'unit_price' => $v['unit_price'],
                    'subtotal' => $v['subtotal'],
                    'stock_replaced' => $v['stock_replaced'] ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $refundAmount = $request->refund_amount ?? $totalRefundAmount;
            $exchangeValue = $request->exchange_value ?? $exchangeSubtotal;

            $saleReturn = SaleReturn::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_number' => $this->generateReturnNumber(),
                'return_date' => $request->return_date,
                'return_type' => $returnType,
                'transaction_type' => 'return',
                'reason' => $request->reason,
                'reason_notes' => $request->reason_notes,
                'refund_method' => $request->refund_method,
                'refund_amount' => $refundAmount,
                'exchange_value' => $exchangeValue,
            ]);

            foreach ($returnItemsToInsert as &$returnItem) {
                $returnItem['sale_return_id'] = $saleReturn->id;
            }
            SaleReturnItem::insert($returnItemsToInsert);

            foreach ($stockMovements as &$movement) {
                $movement['reference_id'] = $saleReturn->id;
            }
            if (!empty($stockMovements)) {
                StockMovement::insert($stockMovements);
            }

            // 2. Process Exchange Items (Stock Out)
            $exchangeItemsToInsert = [];
            $exchangeStockMovements = [];

            foreach ($validatedExchangeItems as $v) {
                $product = $v['product'];
                $quantity = $v['quantity'];

                $beforeQty = (float) $product->stock_quantity;
                $afterQty = $beforeQty - $quantity;

                $product->increment('stock_out', $quantity);
                $product->decrement('stock_quantity', $quantity);

                $exchangeStockMovements[] = [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'type' => 'out',
                    'reference_type' => 'sale_return_exchange',
                    'reference_id' => $saleReturn->id,
                    'quantity' => $quantity,
                    'unit_cost' => $v['cost_price'],
                    'before_quantity' => $beforeQty,
                    'after_quantity' => $afterQty,
                    'notes' => "Exchange out: {$quantity} {$v['name']}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $exchangeItemsToInsert[] = [
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $product->id,
                    'name' => $v['name'],
                    'quantity' => $quantity,
                    'unit_price' => $v['unit_price'],
                    'cost_price' => $v['cost_price'],
                    'exchange_value' => 0,
                    'subtotal' => $v['subtotal'],
                    'total' => $v['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($exchangeItemsToInsert)) {
                ExchangeItem::insert($exchangeItemsToInsert);
            }
            if (!empty($exchangeStockMovements)) {
                StockMovement::insert($exchangeStockMovements);
            }

            $this->recalculateSaleReturnStatus($sale);
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return processed successfully!');
    }

    public function show($id)
    {
        $saleReturn = SaleReturn::with(['sale', 'customer', 'items.product', 'exchangeItems.product'])
            ->whereHas('sale', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        return view('admin.sale-returns.show', compact('saleReturn'));
    }

    public function destroy($id)
    {
        $saleReturn = SaleReturn::with(['items', 'exchangeItems', 'sale'])
            ->whereHas('sale', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        DB::transaction(function () use ($saleReturn) {
            $sale = $saleReturn->sale;
            $productIds = $saleReturn->items->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Revert Return Stock
            foreach ($saleReturn->items as $returnItem) {
                $saleItem = SaleItem::find($returnItem->sale_item_id);
                if ($saleItem) {
                    $saleItem->decrement('quantity_returned', $returnItem->quantity);
                }

                if ($returnItem->stock_replaced) {
                    $product = $products->get($returnItem->product_id);
                    if ($product) {
                        $product->increment('stock_out', $returnItem->quantity);
                        $product->decrement('stock_quantity', $returnItem->quantity);
                    }
                }
            }

            // Revert Exchange Stock
            $exchangeProductIds = $saleReturn->exchangeItems->pluck('product_id')->unique();
            $exchangeProducts = Product::whereIn('id', $exchangeProductIds)->get()->keyBy('id');

            foreach ($saleReturn->exchangeItems as $exchangeItem) {
                $product = $exchangeProducts->get($exchangeItem->product_id);
                if ($product) {
                    $product->decrement('stock_out', $exchangeItem->quantity);
                    $product->increment('stock_quantity', $exchangeItem->quantity);
                }
            }

            StockMovement::where('reference_type', 'like', 'sale_return%')
                ->where('reference_id', $saleReturn->id)
                ->delete();

            $saleReturn->items()->delete();
            $saleReturn->exchangeItems()->delete();
            $saleReturn->delete();

            if ($sale) {
                $this->recalculateSaleReturnStatus($sale);
            }
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return deleted successfully!');
    }

    private function recalculateSaleReturnStatus($sale)
    {
        $totalReturnedQty = (float) SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))->sum('quantity');
        $totalSoldQty = (float) $sale->items()->sum('quantity');
        $totalReturnedAmount = (float) SaleReturn::where('sale_id', $sale->id)->sum('refund_amount');

        $hasReturn = $totalReturnedQty > 0;

        if (!$hasReturn) {
            $sale->updateQuietly(['has_return' => false, 'return_status' => 'none', 'returned_amount' => 0]);
        } elseif ($totalReturnedQty >= $totalSoldQty) {
            $sale->updateQuietly(['has_return' => true, 'return_status' => 'full', 'returned_amount' => $totalReturnedAmount]);
        } else {
            $sale->updateQuietly(['has_return' => true, 'return_status' => 'partial', 'returned_amount' => $totalReturnedAmount]);
        }
    }

    private function generateReturnNumber(): string
    {
        $prefix = 'RMA-';
        $lastReturn = SaleReturn::orderBy('id', 'desc')->first();

        if ($lastReturn && str_starts_with($lastReturn->return_number, $prefix)) {
            $lastNumber = (int) substr($lastReturn->return_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
