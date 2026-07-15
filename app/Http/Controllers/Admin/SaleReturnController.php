<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
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
        $returns = $this->buildReturnQuery($request)
            ->has('items')
            ->latest('created_at')
            ->paginate(15);

        $sales = Sale::select('id', 'invoice_number', 'customer_id')
            ->with('customer:id,name')
            ->latest()
            ->take(100)
            ->get();

        $customers = Customer::select('id', 'name', 'phone')->get();

        return view('admin.sale-returns.index', compact('returns', 'sales', 'customers'));
    }

    public function exchanges(Request $request)
    {
        $exchanges = $this->buildReturnQuery($request)
            ->has('exchangeItems')
            ->latest('created_at')
            ->paginate(15);

        $sales = Sale::select('id', 'invoice_number', 'customer_id')
            ->with('customer:id,name')
            ->latest()
            ->take(100)
            ->get();

        $customers = Customer::select('id', 'name', 'phone')->get();

        return view('admin.sale-exchanges.index', compact('exchanges', 'sales', 'customers'));
    }

    private function buildReturnQuery(Request $request)
    {
        $query = SaleReturn::with(['sale', 'customer', 'employee', 'items.product', 'exchangeItems.product'])
            ->withCount(['items', 'exchangeItems']);

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('returned_id', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        if ($request->has('sale_id') && $request->filled('sale_id')) {
            $query->where('sale_id', $request->input('sale_id'));
        }

        if ($request->has('refund_method') && $request->filled('refund_method')) {
            $query->where('refund_method', $request->input('refund_method'));
        }

        if ($request->has('from_date') && $request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date') && $request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        return $query;
    }

    public function show($id)
    {
        $saleReturn = SaleReturn::with(['sale', 'customer', 'employee', 'items.product', 'exchangeItems.product'])
            ->withCount('items')
            ->findOrFail($id);

        return view('admin.sale-returns.show', compact('saleReturn'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'return_type' => 'required|in:partial,full',
            'reason' => 'required|string|max:255',
            'refund_method' => 'required|string|in:cash,bank,mobile_banking,store_credit',
            'refund_amount' => 'nullable|numeric|min:0',
            'exchange_value' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:1000',
            'employee_id' => 'nullable|exists:users,id',
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

        $sale = Sale::findOrFail($validated['sale_id']);

        if ($validated['return_type'] === 'full') {
            $existingFull = SaleReturn::where('sale_id', $sale->id)->where('return_type', 'full')->exists();
            if ($existingFull) {
                return back()->withInput()->with('error', 'This sale has already been fully returned.');
            }
        }

        $returnItemsData = $validated['items'];

        $saleItems = $sale->items()->get()->keyBy('id');
        $validatedItems = [];
        $totalRefundAmount = 0;

        foreach ($returnItemsData as $item) {
            $saleItem = $saleItems->get($item['sale_item_id']);
            if (!$saleItem) {
                return back()->withInput()->with('error', 'Sale item not found in this sale.');
            }

            $availableQty = (float) $saleItem->quantity - (float) $saleItem->quantity_returned;
            $returnQty = (float) $item['quantity'];

            if ($returnQty > $availableQty) {
                return back()->withInput()->with('error', "Return quantity for {$saleItem->product_name} exceeds available ({$availableQty}).");
            }

            $product = Product::find($item['product_id']);
            $unitPrice = (float) $saleItem->unit_price;
            $itemSubtotal = $returnQty * $unitPrice;
            $totalRefundAmount += $itemSubtotal;

            $validatedItems[] = [
                'saleItem' => $saleItem,
                'product' => $product,
                'name' => $saleItem->product_name,
                'variantName' => $saleItem->variant_name ?? null,
                'quantity' => $returnQty,
                'unitPrice' => $unitPrice,
                'subtotal' => $itemSubtotal,
                'stockReplaced' => (bool) $item['stock_replaced'],
            ];
        }

        $exchangeItemsData = $request->input('exchange_items', []);
        $validatedExchangeItems = [];
        $exchangeSubtotal = 0;

        foreach ($exchangeItemsData as $ex) {
            $product = Product::find($ex['product_id']);
            $quantity = (float) $ex['quantity'];
            $unitPrice = (float) $ex['unit_price'];

            if ($quantity > (float) $product->stock_quantity) {
                return back()->withInput()->with('error', "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}, requested: {$quantity}");
            }

            $itemSubtotal = $quantity * $unitPrice;
            $exchangeSubtotal += $itemSubtotal;

            $validatedExchangeItems[] = [
                'product' => $product,
                'name' => $product->name,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice,
                'costPrice' => (float) $product->cost_price,
                'subtotal' => $itemSubtotal,
            ];
        }

        DB::transaction(function () use ($request, $sale, $validatedItems, $validatedExchangeItems, $totalRefundAmount, $exchangeSubtotal) {
            $returnItemsToInsert = [];
            $stockMovements = [];

            foreach ($validatedItems as $v) {
                $saleItem = $v['saleItem'];
                $product = $v['product'];
                $returnQty = $v['quantity'];

                $saleItem->increment('quantity_returned', $returnQty);

                if ($v['stockReplaced']) {
                    $beforeQty = (float) $product->stock_quantity;
                    $afterQty = $beforeQty + $returnQty;

                    $product->decrement('stock_out', $returnQty);
                    $product->increment('stock_quantity', $returnQty);

                    $stockMovements[] = [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'type' => \App\Enums\StockMovementType::RETURN->value,
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
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'sku' => $product->sku ?? null,
                    'product_name' => $v['name'],
                    'variant_name' => $v['variantName'],
                    'quantity' => $returnQty,
                    'unit_price' => $v['unitPrice'],
                    'is_exchanged' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $refundAmount = $request->refund_amount ?? $totalRefundAmount;
            $exchangeValue = $request->exchange_value ?? $exchangeSubtotal;

            $saleReturn = SaleReturn::create([
                'sale_id' => $sale->id,
                'returned_id' => $this->generateReturnNumber(),
                'customer_id' => $sale->customer_id,
                'order_number' => $sale->invoice_number,
                'return_type' => $request->return_type,
                'reason' => $request->reason,
                'remarks' => $request->remarks,
                'refund_method' => $request->refund_method,
                'refund_amount' => $refundAmount,
                'employee_id' => $request->employee_id ?? Auth::id(),
                'created_at' => $request->return_date,
                'updated_at' => now(),
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
                    'type' => \App\Enums\StockMovementType::RETURN->value,
                    'reference_type' => 'sale_return_exchange',
                    'reference_id' => $saleReturn->id,
                    'quantity' => $quantity,
                    'unit_cost' => $v['costPrice'],
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
                    'unit_price' => $v['unitPrice'],
                    'cost_price' => $v['costPrice'],
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

        return redirect()->route('admin.saleReturns.index')->with('success', 'Sale return processed successfully!');
    }

    public function searchProducts(Request $request)
    {
        $term = $request->get('term', '');
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            })
            ->take(20)
            ->get(['id', 'name', 'sku', 'selling_price', 'stock_quantity']);

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'price' => $p->selling_price,
            'stock' => $p->stock_quantity,
        ]));
    }

    public function destroy($id)
    {
        $saleReturn = SaleReturn::with(['items', 'exchangeItems', 'sale'])
            ->findOrFail($id);

        DB::transaction(function () use ($saleReturn) {
            $sale = $saleReturn->sale;
            $productIds = $saleReturn->items->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($saleReturn->items as $returnItem) {
                $saleItem = SaleItem::find($returnItem->sale_item_id ?? null);
                if ($saleItem) {
                    $saleItem->decrement('quantity_returned', $returnItem->quantity);
                }

                if ($returnItem->is_exchanged == 0) {
                    $product = $products->get($returnItem->product_id);
                    if ($product) {
                        $product->increment('stock_out', $returnItem->quantity);
                        $product->decrement('stock_quantity', $returnItem->quantity);
                    }
                }
            }

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

        return redirect()->route('admin.saleReturns.index')->with('success', 'Sale return deleted successfully!');
    }

    private function generateReturnNumber(): string
    {
        $prefix = 'RMA-';
        $lastReturn = SaleReturn::orderBy('id', 'desc')->first();

        if ($lastReturn && str_starts_with((string) $lastReturn->returned_id, $prefix)) {
            $lastNumber = (int) substr((string) $lastReturn->returned_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
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
}
