<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethodType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $sale = null;

        if ($request->invoice_number) {
            $sale = Sale::with(['items.product', 'customer'])
                ->where('invoice_number', $request->invoice_number)
                ->first();
        }

        $cartData = $this->getCartResponse($request->invoice_number);
        [$start, $end] = $this->businessDayRange();

        $sales = Sale::where('invoice_number', 'like', 'POS-%')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $cashRegisterData = $this->getCashRegisterData($start, $end, $sales->sum('paid_amount'));
        $cashRegister = $cashRegisterData['cashRegister'];

        return view('admin.pos.index', compact('categories', 'employees', 'cartData', 'sale', 'cashRegister', 'cashRegisterData'));
    }

    public function getProducts()
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'selling_price', 'image', 'sku', 'stock_quantity', 'category_id', 'unit_id', 'cost_price')
            ->with('category:id,name')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        $categoryId = $request->get('category_id');

        $products = Product::where('is_active', true)
            ->select('id', 'name', 'selling_price', 'image', 'sku', 'stock_quantity', 'category_id', 'unit_id', 'cost_price')
            ->with('category:id,name')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->limit(50)
            ->get();

        return response()->json($products);
    }

    public function searchCustomers(Request $request)
    {
        $term = $request->term;

        $customers = Customer::select('id', 'name as value', 'phone')
            ->where('name', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->limit(8)
            ->get();

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.sku' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'due' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string',
            'employee_id' => 'nullable|integer|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'cash_received' => 'nullable|numeric',
            'cash_returned' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $customer_id = null;
            if ($request->filled('customer_name') && $request->filled('customer_phone')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );
                $customer_id = $customer->id;
            }

            $due = (float) $request->due;
            $payable = (float) $request->payable;

            if ($due <= 0) {
                $payment_status = PaymentStatus::PAID->value;
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID->value;
            } else {
                $payment_status = PaymentStatus::PARTIAL->value;
            }

            $invoiceNumber = $this->generateInvoiceNumber();

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'employee_id' => $request->employee_id ?? Auth::id(),
                'customer_id' => $customer_id,
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? $request->discount ?? 0,
                'discount_amount' => $request->discount ?? 0,
                'total_amount' => $request->total,
                'payable' => $request->payable,
                'paid_amount' => $request->paid,
                'due_amount' => $request->due,
                'cash_received' => $request->cash_received,
                'change_amount' => $request->cash_returned,
                'status' => SaleStatus::COMPLETED->value,
                'payment_method' => $this->mapPaymentMethod($request->payment_method),
                'payment_status' => $payment_status,
                'paid_at' => now(),
            ]);

            if ((float) $request->paid > 0 && method_exists($sale, 'recordPayment')) {
                $sale->recordPayment(
                    amount: (float) $request->paid,
                    method: $this->mapPaymentMethod($request->payment_method),
                    transactionId: null,
                    notes: 'POS Sale payment'
                );
            }

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['price'];
                $lineSubtotal = $unitPrice * $qty;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $product?->unit_id ?? 1,
                    'product_name' => $item['product_name'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price' => $product?->cost_price ?? 0,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineSubtotal,
                ]);

                if ($product) {
                    $beforeQty = (float) $product->stock_quantity;
                    $afterQty = $beforeQty - $qty;

                    $product->stock_out = (float) $product->stock_out + $qty;
                    $product->stock_quantity = $afterQty;
                    $product->save();

                    StockMovement::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => StockMovementType::SALE->value,
                        'reference_type' => 'pos_sale',
                        'reference_id' => $sale->id,
                        'quantity' => $qty,
                        'unit_cost' => $product->cost_price ?? 0,
                        'before_quantity' => $beforeQty,
                        'after_quantity' => $afterQty,
                        'notes' => "POS Sale: {$sale->invoice_number}",
                    ]);
                }
            }

            if ($request->filled('cart_id')) {
                $cart = Cart::find($request->cart_id);
                if ($cart) {
                    $cart->items()->delete();
                    $cart->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveDraft(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'string',
            'employee_id' => 'nullable|integer|exists:users,id',
            'cash_received' => 'nullable|numeric',
            'cash_returned' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $customer_id = null;
            if ($request->filled('customer_name') && $request->filled('customer_phone')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );
                $customer_id = $customer->id;
            }

            $invoiceNumber = $this->generateInvoiceNumber();
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'customer_id' => $customer_id,
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? $request->discount ?? 0,
                'discount_amount' => $request->discount ?? 0,
                'total_amount' => $request->total,
                'payable' => $request->total,
                'paid_amount' => 0,
                'due_amount' => $request->total,
                'cash_received' => $request->cash_received ?? 0,
                'change_amount' => $request->cash_returned ?? 0,
                'employee_id' => $request->employee_id ?? Auth::id(),
                'status' => SaleStatus::DRAFT->value,
                'payment_method' => $this->mapPaymentMethod($request->payment_method),
                'payment_status' => PaymentStatus::UNPAID->value,
                'notes' => 'POS Draft Sale',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['price'];
                $lineSubtotal = $unitPrice * $qty;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => null,
                    'unit_id' => $product?->unit_id ?? 1,
                    'product_name' => $item['product_name'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price' => $product?->cost_price ?? 0,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineSubtotal,
                ]);
            }

            if ($request->filled('cart_id')) {
                $cart = Cart::find($request->cart_id);
                if ($cart) {
                    $cart->items()->delete();
                    $cart->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully',
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrCreateCart($invoiceNumber = null)
    {
        if ($invoiceNumber) {
            $cart = Cart::where('invoice_number', $invoiceNumber)->first();

            if (!$cart) {
                // Hydrate a cart from an existing sale so it can be edited in POS
                $draft = Sale::where('invoice_number', $invoiceNumber)->first();

                if ($draft) {
                    $cart = Cart::create([
                        'invoice_number' => $invoiceNumber,
                        'user_id' => Auth::id(),
                    ]);

                    foreach ($draft->items as $item) {
                         CartItem::create([
                            'cart_id' => $cart->id,
                            'product_id' => $item->product_id,
                            'quantity' => (float) $item->quantity,
                            'item_unit_price' => (float) ($item->unit_price ?? $item->product->selling_price ?? 0),
                            'item_total_price' => (float) ($item->unit_price ?? $item->product->selling_price ?? 0) * (float) $item->quantity,
                        ]);
                    }
                } else {
                    $cart = Cart::firstOrCreate(
                        ['invoice_number' => $invoiceNumber],
                        ['user_id' => Auth::id()]
                    );
                }
            }

            return $cart;
        }

        return Cart::firstOrCreate(
            ['invoice_number' => null],
            ['user_id' => Auth::id()]
        );
    }

    public function getCart(Request $request)
    {
        return response()->json([
            'success' => true,
            'cart' => $this->getCartResponse($request->invoice_number)
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'invoice_number' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $cart = $this->getOrCreateCart($request->invoice_number);
            $product = Product::findOrFail($request->product_id);

            if ($product->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} items available in stock"
                ], 400);
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                $newQuantity = (float) $cartItem->quantity + (float) $request->quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add more. Only {$product->stock_quantity} available"
                    ], 400);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => (float) $request->quantity,
                    'item_unit_price' => (float) ($product->selling_price ?? 0),
                    'item_total_price' => (float) ($product->selling_price ?? 0) * (float) $request->quantity,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart' => $this->getCartResponse($request->invoice_number)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|numeric|min:0.01', 'invoice_number' => 'nullable|string']);

        try {
            $cart = $this->getOrCreateCart($request->invoice_number);
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();
            $product = Product::find($cartItem->product_id);

            if ($product && $request->quantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->stock_quantity} available"
                ], 400);
            }

            $cartItem->quantity = (float) $request->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'cart' => $this->getCartResponse($request->invoice_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function removeItem(Request $request, $itemId)
    {
        try {
            $cart = $this->getOrCreateCart($request->invoice_number);
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed',
                'cart' => $this->getCartResponse($request->invoice_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clearCart(Request $request)
    {
        try {
            $invoiceNumber = $request->invoice_number ?? null;
            $cart = $this->getOrCreateCart($invoiceNumber);
            $cart->items()->delete();
            $cart->delete();

            return response()->json(['success' => true, 'message' => 'Cart cleared']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function posSales(Request $request)
    {
        $query = Sale::with(['customer'])
            ->where('invoice_number', 'like', 'POS-%');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('sale_date', [$request->date_from, $request->date_to]);
        }

        $sales = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.pos.sales', compact('sales'));
    }

    public function saleShow($invoice_number)
    {
        $sale = Sale::with(['customer', 'items.product'])->where('invoice_number', $invoice_number)->firstOrFail();
        return view('admin.pos.sale_show', compact('sale'));
    }

    public function saleDelete($id)
    {
        $sale = Sale::findOrFail($id);

        // Optional: Revert stock here if needed before deletion
        $sale->delete();

        return redirect()->route('admin.pos.sales.index')->with('success', 'Sale deleted successfully!');
    }

    public function receipt($invoiceNumber)
    {
        $sale = Sale::where('invoice_number', $invoiceNumber)->with(['customer', 'items.product'])->firstOrFail();
        return view('admin.pos.receipt', compact('sale'));
    }

    public function getPosOrders(Request $request)
    {
        $type = $request->get('type', 'sales');

        $query = Sale::with('customer:id,name,phone')
            ->where('invoice_number', 'like', 'POS-%');

        if ($type === 'draft') {
            $query->where('status', SaleStatus::DRAFT->value);
        } else {
            [$start, $end] = $this->businessDayRange();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $sales = $query->latest()->get();

        $data = $sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'customer_name' => $sale->customer?->name ?? 'Walk-in Customer',
                'total' => number_format($sale->total_amount, 2),
                'status' => $sale->status,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $orderId)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.sku' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'due' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string',
            'employee_id' => 'nullable|integer|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'cash_received' => 'nullable|numeric',
            'cash_returned' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($orderId);

            $customer_id = $sale->customer_id;
            if ($request->filled('customer_name') && $request->filled('customer_phone')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );
                $customer_id = $customer->id;
            }

            $due = (float) $request->due;
            $payable = (float) $request->payable;

            if ($due <= 0) {
                $payment_status = PaymentStatus::PAID->value;
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID->value;
            } else {
                $payment_status = PaymentStatus::PARTIAL->value;
            }

            $sale->update([
                'customer_id' => $customer_id,
                'employee_id' => $request->employee_id ?? $sale->employee_id ?? Auth::id(),
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? $request->discount ?? 0,
                'discount_amount' => $request->discount ?? 0,
                'total_amount' => $request->total,
                'payable' => $request->payable,
                'paid_amount' => $request->paid,
                'due_amount' => $request->due,
                'cash_received' => $request->cash_received,
                'change_amount' => $request->cash_returned,
                'status' => SaleStatus::COMPLETED->value,
                'payment_method' => $this->mapPaymentMethod($request->payment_method),
                'payment_status' => $payment_status,
                'paid_at' => now(),
            ]);

            // Revert old stock (only if it was already deducted — drafts never deduct on save)
            if ($sale->status !== SaleStatus::DRAFT->value) {
                $oldItems = $sale->items;
                foreach ($oldItems as $oldItem) {
                    $product = Product::find($oldItem->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $oldItem->quantity);
                        $product->decrement('stock_out', $oldItem->quantity);
                    }
                }
            }
            $sale->items()->delete();

            // Clear old payments for simplicity on update (or handle complex reconciliation)
            $sale->payments()->delete();

            if ((float) $request->paid > 0 && method_exists($sale, 'recordPayment')) {
                $sale->recordPayment(
                    amount: (float) $request->paid,
                    method: $this->mapPaymentMethod($request->payment_method),
                    transactionId: null,
                    notes: 'POS Sale payment updated'
                );
            }

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['price'];
                $lineSubtotal = $unitPrice * $qty;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => null,
                    'unit_id' => $product?->unit_id ?? 1,
                    'product_name' => $item['product_name'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price' => $product?->cost_price ?? 0,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineSubtotal,
                ]);

                if ($product) {
                    $beforeQty = (float) $product->stock_quantity;
                    $afterQty = $beforeQty - $qty;

                    $product->stock_out = (float) $product->stock_out + $qty;
                    $product->stock_quantity = $afterQty;
                    $product->save();

                    StockMovement::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => StockMovementType::SALE->value,
                        'reference_type' => 'pos_sale',
                        'reference_id' => $sale->id,
                        'quantity' => $qty,
                        'unit_cost' => $product->cost_price ?? 0,
                        'before_quantity' => $beforeQty,
                        'after_quantity' => $afterQty,
                        'notes' => "POS Sale Updated: {$sale->invoice_number}",
                    ]);
                }
            }

            DB::commit();

            if ($sale->invoice_number) {
                $cart = Cart::where('invoice_number', $sale->invoice_number)->first();
                if ($cart) {
                    $cart->items()->delete();
                    $cart->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale updated successfully',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateItemPrice(Request $request, $itemId)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'invoice_number' => 'nullable|string',
        ]);

        try {
            $cart = $this->getOrCreateCart($request->invoice_number);
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();
            $cartItem->item_unit_price = (float) $request->price;
            $cartItem->item_total_price = (float) $request->price * (float) $cartItem->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Price updated',
                'cart' => $this->getCartResponse($request->invoice_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getCartResponse($invoiceNumber = null)
    {
        $cart = $this->getOrCreateCart($invoiceNumber);
        $cart->load('items.product');

        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            $price = (float) ($item->item_unit_price ?? $product->selling_price);
            $qty = (float) $item->quantity;

            return [
                'id' => $item->id,
                'source' => 'cart',
                'product_id' => $item->product_id,
                'sku' => $product->sku ?? '',
                'product_name' => $product->name ?? 'Unknown',
                'product_image' => $product->image ? asset('storage/' . $product->image) : asset('assets/images/default.png'),
                'quantity' => $qty,
                'price' => $price,
                'stock' => (float) $product->stock_quantity,
                'total_price' => $qty * $price,
            ];
        });

        $subtotal = $items->sum('total_price');

        return [
            'id' => $cart->id,
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'items_count' => $items->count(),
            'total_items' => $items->sum('quantity'),
        ];
    }

    private function getCashRegisterData($start, $end, $ordersTotal)
    {
        $cashRegister = CashRegister::whereNull('closed_at')->first();
        $expense = Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $salesReturns = SaleReturn::whereBetween('created_at', [$start, $end])->sum('refund_amount');

        return [
            'cashRegister' => $cashRegister,
            'opening_amount' => $cashRegister ? $cashRegister->opening_amount : 0,
            'sales_amount' => $ordersTotal,
            'expense' => $expense,
            'sales_returns' => $salesReturns,
        ];
    }

    private function businessDayRange()
    {
        $start = Carbon::today()->startOfDay();
        $end = Carbon::today()->endOfDay();
        return [$start, $end];
    }

    private function mapPaymentMethod(?string $method): string
    {
        return match (strtolower($method)) {
            'bkash', 'nagad', 'mobile_banking' => PaymentMethodType::MOBILE_BANKING->value,
            'bank' => PaymentMethodType::BANK->value,
            'card' => PaymentMethodType::CARD->value,
            'cash' => PaymentMethodType::CASH->value,
            'none',null, '' => PaymentMethodType::NONE->value,
            default => PaymentMethodType::OTHER->value,
        };
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $lastSale = Sale::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->invoice_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
