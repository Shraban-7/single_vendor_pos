<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Purchase::with('supplier')->where('user_id', $userId);

        if ($request->has('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->input('to_date'));
        }
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        $statsQuery = clone $query;
        $stats = [
            'total_subtotal' => $statsQuery->sum('subtotal'),
            'total_due' => $statsQuery->sum('due_amount'),
        ];

        $purchases = $query->latest('purchase_date')->paginate(15);
        $suppliers = Supplier::where('user_id', $userId)->select('id', 'name')->get();

        return view('admin.purchases.index', compact('purchases', 'stats', 'suppliers'));
    }

    public function create()
    {
        $userId = Auth::id();
        $suppliers = Supplier::where('user_id', $userId)->where('is_active', true)->select('id', 'name', 'company_name')->get();

        // Fetch products with cost price to pre-fill unit price
        $products = Product::where('user_id', $userId)
            ->where('is_active', true)
            ->select('id', 'name', 'cost_price', 'unit_id')
            ->with('unit:id,name,short_name')
            ->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_note' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['purchase_number'] = $this->generatePurchaseNumber();
        $data['paid_amount'] = $data['paid_amount'] ?? 0;

        $itemsData = $request->input('items');
        $subtotal = collect($itemsData)->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $data['subtotal'] = $subtotal;
        $data['due_amount'] = max(0, $subtotal - $data['paid_amount']);
        $data['payment_status'] = $data['due_amount'] <= 0 ? PaymentStatus::PAID->value : ($data['paid_amount'] > 0 ? 'partial' : 'unpaid');

        $supplier = Supplier::find($data['supplier_id']);

        DB::transaction(function () use ($data, $itemsData, $supplier) {
            $purchase = Purchase::create($data);
            $productIds = collect($itemsData)->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)->with('unit')->get()->keyBy('id');

            $purchaseItems = [];
            foreach ($itemsData as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $purchaseItems[] = [
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'name' => $product->name,
                    'unit_id' => $product->unit_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $beforeQty = (float) $product->stock_quantity;
                $afterQty = $beforeQty + $quantity;

                $product->increment('stock_in', $quantity);
                $product->increment('stock_quantity', $quantity);

                StockMovement::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'type' => StockMovementType::PURCHASE,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitPrice,
                    'before_quantity' => $beforeQty,
                    'after_quantity' => $afterQty,
                    'notes' => "Purchased {$quantity} {$product->unit->short_name} of {$product->name}",
                ]);
            }

            PurchaseItem::insert($purchaseItems);

            $supplier->increment('total_purchases', $purchase->subtotal);
            $supplier->increment('purchase_count', 1);
            if ($purchase->due_amount > 0) {
                $supplier->increment('current_balance', $purchase->due_amount);
            }
            if ($data['paid_amount'] > 0) {
                $supplier->increment('total_paid', $data['paid_amount']);
            }
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase created successfully!');
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.unit', 'payments'])->where('user_id', Auth::id())->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = Purchase::with('items')->where('user_id', Auth::id())->findOrFail($id);
        $userId = Auth::id();

        $suppliers = Supplier::where('user_id', $userId)->where('is_active', true)->select('id', 'name', 'company_name')->get();
        $products = Product::where('user_id', $userId)
            ->where('is_active', true)
            ->select('id', 'name', 'cost_price', 'unit_id')
            ->with('unit:id,name,short_name')
            ->get();

        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:purchase_date',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_note' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = $validated;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $itemsData = $request->input('items');
        $data['subtotal'] = collect($itemsData)->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $data['due_amount'] = max(0, $data['subtotal'] - $data['paid_amount']);
        $data['payment_status'] = $data['due_amount'] <= 0 ? PaymentStatus::PAID->value : ($data['paid_amount'] > 0 ? 'partial' : 'unpaid');

        DB::transaction(function () use ($purchase, $data, $itemsData) {
            $oldItems = $purchase->items()->get();
            $oldSupplierId = $purchase->supplier_id;
            $oldSubtotal = $purchase->subtotal;
            $oldPaidAmount = $purchase->paid_amount;
            $oldDueAmount = $purchase->due_amount;

            // 1. Revert old stock
            $oldProductIds = $oldItems->pluck('product_id')->unique();
            $oldProducts = Product::whereIn('id', $oldProductIds)->with('unit')->get()->keyBy('id');

            foreach ($oldItems as $oldItem) {
                $product = $oldProducts->get($oldItem->product_id);
                $quantity = (float) $oldItem->quantity;
                if ($product) {
                    $product->decrement('stock_in', $quantity);
                    $product->decrement('stock_quantity', $quantity);
                }
            }

            StockMovement::where('reference_type', 'purchase')->where('reference_id', $purchase->id)->delete();
            $purchase->items()->delete();

            // 2. Apply new data
            $purchase->update($data);
            $newProductIds = collect($itemsData)->pluck('product_id')->unique();
            $newProducts = Product::whereIn('id', $newProductIds)->with('unit')->get()->keyBy('id');

            $purchaseItems = [];
            foreach ($itemsData as $item) {
                $product = $newProducts->get($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $purchaseItems[] = [
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'name' => $product->name,
                    'unit_id' => $product->unit_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $beforeQty = (float) $product->stock_quantity;
                $afterQty = $beforeQty + $quantity;

                $product->increment('stock_in', $quantity);
                $product->increment('stock_quantity', $quantity);

                StockMovement::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'type' => StockMovementType::PURCHASE,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitPrice,
                    'before_quantity' => $beforeQty,
                    'after_quantity' => $afterQty,
                    'notes' => "Purchase updated: {$quantity} {$product->unit->short_name} of {$product->name}",
                ]);
            }
            PurchaseItem::insert($purchaseItems);

            // 3. Revert old supplier
            if ($oldSupplierId) {
                $oldSupplier = Supplier::find($oldSupplierId);
                if ($oldSupplier) {
                    $oldSupplier->decrement('total_purchases', $oldSubtotal);
                    $oldSupplier->decrement('purchase_count', 1);
                    $oldSupplier->decrement('total_paid', $oldPaidAmount);
                    if ($oldDueAmount > 0) $oldSupplier->decrement('current_balance', $oldDueAmount);
                }
            }

            // 4. Apply new supplier
            $newSupplier = Supplier::find($data['supplier_id']);
            if ($newSupplier) {
                $newSupplier->increment('total_purchases', $data['subtotal']);
                $newSupplier->increment('purchase_count', 1);
                if ($data['paid_amount'] > 0) $newSupplier->increment('total_paid', $data['paid_amount']);
                if ($data['due_amount'] > 0) $newSupplier->increment('current_balance', $data['due_amount']);
            }
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase updated successfully!');
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('user_id', Auth::id())->findOrFail($id);
        $supplier = $purchase->supplier;

        DB::transaction(function () use ($purchase, $supplier) {
            $items = $purchase->items()->get();
            $productIds = $items->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                $quantity = (float) $item->quantity;
                if ($product) {
                    $product->decrement('stock_in', $quantity);
                    $product->decrement('stock_quantity', $quantity);
                }
            }

            StockMovement::where('reference_type', 'purchase')->where('reference_id', $purchase->id)->delete();

            if ($supplier) {
                $supplier->decrement('total_purchases', $purchase->subtotal);
                $supplier->decrement('purchase_count', 1);
                if ($purchase->due_amount > 0) $supplier->decrement('current_balance', $purchase->due_amount);
                if ($purchase->paid_amount > 0) $supplier->decrement('total_paid', $purchase->paid_amount);
            }

            $purchase->items()->delete();
            $purchase->payments()->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted successfully!');
    }

    public function makePayment(Request $request, $id)
    {
        $purchase = Purchase::where('user_id', Auth::id())->findOrFail($id);
        $supplier = $purchase->supplier;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];

        if ($amount > $purchase->due_amount) {
            return back()->with('error', 'Payment amount exceeds the due amount.');
        }

        DB::transaction(function () use ($purchase, $supplier, $amount, $validated) {
            $purchase->paid_amount += $amount;
            $purchase->due_amount -= $amount;
            $purchase->payment_status = $purchase->due_amount <= 0 ? PaymentStatus::PAID->value : 'partial';
            $purchase->save();

            Payment::create([
                'user_id' => Auth::id(),
                'payment_number' => 'PAY-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'payment_type' => PaymentType::PURCHASE,
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'supplier_id' => $supplier->id,
                'amount' => $amount,
                'payment_date' => now(),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'],
            ]);

            $supplier->current_balance -= $amount;
            $supplier->total_paid += $amount;
            $supplier->save();
        });

        return back()->with('success', 'Payment recorded successfully!');
    }

    private function generatePurchaseNumber(): string
    {
        $prefix = 'PUR-';
        $lastPurchase = Purchase::withTrashed()->where('purchase_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
        $newNumber = $lastPurchase ? (int) substr($lastPurchase->purchase_number, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
