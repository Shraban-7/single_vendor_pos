<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant; // Keep if you still use variants, otherwise safe to ignore
use App\Models\SaleReturn;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        $order = null;

        if ($request->order_number) {
            $order = Order::with(['items.product', 'customer'])
                ->where('order_number', $request->order_number)
                ->where('is_pos', 1)
                ->first();
        }

        $cart = $this->getCart($request);
        [$start, $end] = $this->businessDayRange();

        $orders = Order::where('is_pos', 1)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $cashRegisterData = $this->getCashRegisterData($start, $end, $orders->sum('paid'));
        $cashRegister = $cashRegisterData['cashRegister'];

        return view('admin.pos.index', compact('categories', 'cart', 'order', 'cashRegister', 'cashRegisterData'));
    }

    public function getProducts()
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'selling_price', 'image', 'sku', 'stock_quantity', 'category_id')
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
            ->select('id', 'name', 'selling_price', 'image', 'sku', 'stock_quantity', 'category_id')
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
                $payment_status = PaymentStatus::PAID->value ?? 'paid';
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID->value ?? 'unpaid';
            } else {
                $payment_status = 'partial';
            }

            $order = Order::create([
                'order_number' => 'POS-' . strtoupper(uniqid()),
                'is_pos' => 1,
                'user_id' => Auth::id(),
                'customer_id' => $customer_id,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'total' => $request->total,
                'payable' => $request->payable,
                'paid' => $request->paid,
                'due' => $request->due,
                'cash_received' => $request->cash_received,
                'cash_returned' => $request->cash_returned,
                'status' => OrderStatus::DELIVERED->value ?? 'delivered',
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_status' => $payment_status,
                'paid_at' => now(),
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['sku'],
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                    'subtotal' => (float) $item['price'] * (float) $item['quantity'],
                ]);

                // ✅ FIXED: Properly update stock_quantity and stock_out, and log movement
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product) {
                    $qty = (float) $item['quantity'];
                    $beforeQty = (float) $product->stock_quantity;
                    $afterQty = $beforeQty - $qty;

                    $product->stock_out = (float) $product->stock_out + $qty;
                    $product->stock_quantity = $afterQty;
                    $product->save();

                    StockMovement::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'type' => 'out',
                        'reference_type' => 'pos_sale',
                        'reference_id' => $order->id,
                        'quantity' => $qty,
                        'unit_cost' => (float) $item['price'],
                        'before_quantity' => $beforeQty,
                        'after_quantity' => $afterQty,
                        'notes' => "POS Sale: {$order->order_number}",
                    ]);
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::DELIVERED->value ?? 'delivered',
                'comment' => "POS Order Completed",
                'updated_by' => Auth::id(),
            ]);

            // Clear cart if it exists
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
                'message' => 'Order completed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveDraft(Request $request)
    {
        // Similar validation as store, but status = 'draft' and NO stock deduction
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
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

            $order = Order::create([
                'order_number' => 'POS-DRAFT-' . strtoupper(uniqid()),
                'is_pos' => 1,
                'user_id' => Auth::id(),
                'customer_id' => $customer_id,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'payable' => $request->total,
                'paid' => 0,
                'due' => $request->total,
                'status' => OrderStatus::DRAFT->value ?? 'draft',
                'payment_status' => 'unpaid',
                'notes' => 'POS Draft Order',
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['sku'] ?? null,
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                    'subtotal' => (float) $item['price'] * (float) $item['quantity'],
                ]);
                // NOTE: Draft orders do NOT deduct stock
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
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrCreateCart($orderNumber = null)
    {
        if ($orderNumber) {
            return Cart::firstOrCreate(
                ['order_number' => $orderNumber, 'is_pos' => 1],
                ['user_id' => Auth::id()]
            );
        }

        return Cart::firstOrCreate(
            ['is_pos' => 1, 'order_number' => null],
            ['user_id' => Auth::id()]
        );
    }

    public function getCart(Request $request)
    {
        return response()->json([
            'success' => true,
            'cart' => $this->getCartResponse($request->order_number)
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'order_number' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $cart = $this->getOrCreateCart($request->order_number);
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
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|numeric|min:0.01', 'order_number' => 'nullable|string']);

        try {
            $cart = $this->getOrCreateCart($request->order_number);
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
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function removeItem(Request $request, $itemId)
    {
        try {
            $cart = $this->getOrCreateCart($request->order_number);
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed',
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clearCart()
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->items()->delete();
            $cart->delete();

            return response()->json(['success' => true, 'message' => 'Cart cleared']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function posSales(Request $request)
    {
        $query = Order::where('is_pos', 1)->with(['customer']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $orders = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.pos.sales', compact('orders'));
    }

    public function saleShow($order_number)
    {
        $order = Order::with(['customer', 'items.product'])->where('order_number', $order_number)->firstOrFail();
        return view('admin.pos.sale_show', compact('order'));
    }

    public function saleDelete($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.pos.sales.index')->with('success', 'Sale deleted successfully!');
    }

    public function receipt($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['customer', 'items.product'])->firstOrFail();
        return view('admin.pos.receipt', compact('order'));
    }

    public function getPosOrders(Request $request)
    {
        $type = $request->get('type', 'sales');

        $query = Order::where('is_pos', 1)
            ->with('customer:id,name,phone');

        if ($type === 'draft') {
            $query->where('status', OrderStatus::DRAFT->value);
        } else {
            [$start, $end] = $this->businessDayRange();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->latest()->get();

        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name ?? 'Walk-in Customer',
                'total' => number_format($order->total, 2),
                'status' => $order->status,
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
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'cash_received' => 'nullable|numeric',
            'cash_returned' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            $customer_id = $order->customer_id;
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
                $payment_status = PaymentStatus::PAID->value ?? 'paid';
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID->value ?? 'unpaid';
            } else {
                $payment_status = 'partial';
            }

            $order->update([
                'customer_id' => $customer_id,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'total' => $request->total,
                'payable' => $request->payable,
                'paid' => $request->paid,
                'due' => $request->due,
                'cash_received' => $request->cash_received,
                'cash_returned' => $request->cash_returned,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_status' => $payment_status,
            ]);

            $order->items()->delete();

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['sku'],
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                    'subtotal' => (float) $item['price'] * (float) $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
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
            'order_number' => 'nullable|string',
        ]);

        try {
            $cart = $this->getOrCreateCart($request->order_number);
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();

            $cartItem->item_unit_price = (float) $request->price;
            $cartItem->item_total_price = (float) $request->price * (float) $cartItem->quantity;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getCartResponse($orderNumber = null)
    {
        $cart = $this->getOrCreateCart($orderNumber);
        $cart->load('items.product');

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'source' => 'cart',
                'product_id' => $item->product_id,
                'sku' => $item->product->sku ?? '',
                'product_name' => $item->product->name ?? 'Unknown',
                'product_image' => $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default.png'),
                'quantity' => (float) $item->quantity,
                'price' => (float) $item->product->selling_price,
                'stock' => (float) $item->product->stock_quantity,
                'total_price' => (float) $item->quantity * (float) $item->product->selling_price,
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
}
