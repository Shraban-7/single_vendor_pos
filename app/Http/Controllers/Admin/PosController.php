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
use App\Models\ProductVariant;
use App\Models\SaleReturn;
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
            $order = Order::with(['items.product.images', 'customer'])
                ->where('order_number', $request->order_number)
                ->where('is_pos', 1)
                ->first();
        }

        $cart = $this->getCart($request);

        $employees = User::where('role', 'staff')->get();

        [$start, $end] = businessDayRange();

        $orders = Order::where('is_pos', 1)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $cashRegisterData = $this->getCashRegisterData($start, $end, $orders->sum('paid'));
        $cashRegister = $cashRegisterData['cashRegister'];

        return view('admin.pos.index', compact('categories', 'cart', 'employees', 'order', 'cashRegister', 'cashRegisterData'));
    }

    public function getProducts()
    {
        $products = Product::with(['category', 'variants.size', 'variants.color', 'images'])
            ->where('is_active', true)
            ->get()
            ->sortByDesc('created_at')
            ->values();

        return apiResponse([
            'html' => view()->make('components.pos.product-grid', ['products' => $products])->render(),
            'message' => "Products fetched successfully",
            'success' => true
        ]);
    }

    private function getCashRegisterData($start, $end, $ordersTotal)
    {
        $cashRegister = CashRegister::whereBetween('opened_at', [$start, $end])
            ->first();

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

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        $categoryId = $request->get('category_id');

        $products = Product::with(['category', 'variants.size', 'variants.color'])
            ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                });
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

        return Customer::select(
            'id',
            'name as value',
            'phone'
        )
            ->where('name', 'like', "%$term%")
            ->orWhere('phone', 'like', "%$term%")
            ->limit(8)
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.product_image' => 'nullable|string',
            'items.*.sku' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'due' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'discount' => 'nullable',
            'employee_id' => 'nullable',
            'customer_name' => ['nullable', 'string', 'max:255', 'required_with:customer_phone'],
            'customer_phone' => ['nullable', 'string', 'max:20', 'required_with:customer_name'],
            'cash_received' => 'nullable',
            'cash_returned' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $paymentMethodEnum = filled($request->payment_method)
                ? PaymentMethod::tryFrom(strtolower($request->payment_method))
                : null;

            $customer_id = null;

            if (!$customer_id && $request->filled('customer_name') && $request->filled('customer_phone')) {

                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );

                $customer_id = $customer->id;
            }

            $due = (float) $request->due;
            $payable = (float) $request->payable;

            if ($due <= 0) {
                $payment_status = PaymentStatus::PAID;
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID;
            } else {
                $payment_status = PaymentStatus::PARTIAL;
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber('PS'),
                'is_pos' => 1,
                'user_id' => null,
                'customer_id' => $customer_id,
                'employee_id' => $request->employee_id ? $request->employee_id : null,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'shipping_phone' => null,
                'shipping_email' => null,
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'shipping_cost' => 0,
                'tax_amount' => 0,
                'total' => $request->total,
                'payable' => $request->payable,
                'paid' => $request->paid,
                'due' => $request->due,
                'cash_received' => $request->cash_received,
                'cash_returned' => $request->cash_returned,
                'status' => OrderStatus::DELIVERED,
                'payment_method' => $paymentMethodEnum->value ?? '',
                'payment_status' => $payment_status,
                'paid_at' => now(),
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['sku'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'size_name' => $item['size'],
                    'color_name' => $item['color'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'total' => ($item['price'] * $item['quantity']),
                ]);

                if (isset($item['variant_id']) && $item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock_in', $item['quantity']);
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock_in', $item['quantity']);
                    }
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::DELIVERED,
                'comment' => "Order Delivered",
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            $cart = Cart::find($request->cart_id);
            $cart->items()->delete();
            $cart->delete();

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

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.product_image' => 'nullable|string',
            'items.*.sku' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',

            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'due' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',

            'discount' => 'nullable',
            'employee_id' => 'nullable',
            'customer_name' => ['nullable', 'string', 'max:255', 'required_with:customer_phone'],
            'customer_phone' => ['nullable', 'string', 'max:20', 'required_with:customer_name'],

            'cash_received' => 'nullable',
            'cash_returned' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::with('items')->findOrFail($id);

            $paymentMethodEnum = filled($request->payment_method)
                ? PaymentMethod::tryFrom(strtolower($request->payment_method))
                : null;

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
                $payment_status = PaymentStatus::PAID;
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID;
            } else {
                $payment_status = PaymentStatus::PARTIAL;
            }

            $order->update([
                'customer_id' => $customer_id,
                'employee_id' => $request->employee_id ?? null,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',

                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'total' => $request->total,
                'payable' => $request->payable,
                'paid' => $request->paid,
                'due' => $request->due,

                'cash_received' => $request->cash_received,
                'cash_returned' => $request->cash_returned,

                'payment_method' => $paymentMethodEnum->value ?? '',
                'payment_status' => $payment_status,
                'status' => OrderStatus::DELIVERED,

                'paid_at' => now(),
            ]);

            $processedItemIds = [];

            foreach ($request->items as $item) {
                $orderItemId = data_get($item, 'id');

                if ($item['source'] === 'order') {
                    if (is_string($orderItemId) && str_starts_with($orderItemId, 'order_')) {
                        $orderItemId = (int) str_replace('order_', '', $orderItemId);
                    }
                }

                $orderItemId = (int) $orderItemId;

                $orderItem = OrderItem::find($orderItemId);

                if ($orderItem) {
                    $orderItem->update([
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'product_sku' => $item['sku'] ?? null,
                        'product_variant_id' => $item['variant_id'] ?? null,
                        'size_name' => $item['size'] ?? null,
                        'color_name' => $item['color'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);

                    $processedItemIds[] = $orderItem->id;
                } else {
                    $newItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'product_sku' => $item['sku'] ?? null,
                        'product_variant_id' => $item['variant_id'] ?? null,
                        'size_name' => $item['size'] ?? null,
                        'color_name' => $item['color'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);

                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $variant->decrement('stock_in', $item['quantity']);
                        }
                    } else {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $product->decrement('stock_in', $item['quantity']);
                        }
                    }

                    $processedItemIds[] = $newItem->id;
                }
            }

            $order->items()
                ->whereNotIn('id', $processedItemIds)
                ->get()
                ->each(function ($oldItem) {
                    if ($oldItem->product_variant_id) {
                        $variant = ProductVariant::find($oldItem->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock_in', $oldItem->quantity);
                        }
                    } else {
                        $product = Product::find($oldItem->product_id);
                        if ($product) {
                            $product->increment('stock_in', $oldItem->quantity);
                        }
                    }

                    $oldItem->delete();
                });

            activity_log(
                action: 'updated',
                model: $order,
                description: 'Order updated',
            );

            DB::commit();

            $cart = Cart::find($request->cart_id);
            $cart->items()->delete();
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveDraft(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.product_image' => 'nullable|string',
            'items.*.sku' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'due' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'discount' => 'nullable',
            'employee_id' => 'nullable',
            'customer_name' => ['nullable', 'string', 'max:255', 'required_with:customer_phone'],
            'customer_phone' => ['nullable', 'string', 'max:20', 'required_with:customer_name'],
            'cash_received' => 'nullable',
            'cash_returned' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $paymentMethodEnum = filled($request->payment_method)
                ? PaymentMethod::tryFrom(strtolower($request->payment_method))
                : null;

            $customer_id = null;

            if (!$customer_id && $request->filled('customer_name') && $request->filled('customer_phone')) {

                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );

                $customer_id = $customer->id;
            }

            $due = (float) $request->due;
            $payable = (float) $request->payable;

            if ($due <= 0) {
                $payment_status = PaymentStatus::PAID;
            } elseif ($due >= $payable) {
                $payment_status = PaymentStatus::UNPAID;
            } else {
                $payment_status = PaymentStatus::PARTIAL;
            }


            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber('PS'),
                'is_pos' => 1,
                'user_id' => null,
                'customer_id' => $customer_id,
                'employee_id' => $request->employee_id ? $request->employee_id : null,
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'shipping_phone' => null,
                'shipping_email' => null,
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'shipping_cost' => 0,
                'tax_amount' => 0,
                'total' => $request->total,
                'payable' => $request->payable,
                'paid' => $request->paid,
                'due' => $request->due,
                'cash_received' => $request->cash_received,
                'cash_returned' => $request->cash_returned,
                'status' => OrderStatus::DRAFT,
                'payment_method' => $paymentMethodEnum->value ?? '',
                'payment_status' => $payment_status,
                'notes' => 'POS Order',
                'paid_at' => now(),
            ]);

            // Create order items and update stock
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['sku'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'size_name' => $item['size'],
                    'color_name' => $item['color'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'total' => ($item['price'] * $item['quantity']),
                ]);

                // Update stock
                if (isset($item['variant_id']) && $item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock_in', $item['quantity']);
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock_in', $item['quantity']);
                    }
                }
            }

            DB::commit();

            $cart = Cart::find($request->cart_id);
            $cart->items()->delete();
            $cart->delete();

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

    public function getOrCreateCart($orderNumber = null)
    {
        if ($orderNumber) {
            return Cart::firstOrCreate(
                [
                    'order_number' => $orderNumber,
                    'is_pos' => 1
                ],
                [
                    'user_id' => null
                ]
            );
        }

        $cart = Cart::where('is_pos', 1)
            ->whereNull('order_number')
            ->latest()
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'is_pos' => 1,
                'user_id' => null
            ]);
        }

        return $cart;
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
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'order_number' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $cart = $this->getOrCreateCart($request->order_number);

            $product = Product::findOrFail($request->product_id);

            if ($product->variants->count() > 0 && !$request->product_variant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select product options (size/color) before adding to cart',
                    'requires_variant' => true
                ], 400);
            }

            $variant = null;
            if ($request->product_variant_id) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('id', $request->product_variant_id)
                    ->firstOrFail();

                if ($variant->currentStock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected variant does not have enough stock'
                    ], 400);
                }
            } else {
                if ($product->currentStock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product does not have enough stock'
                    ], 400);
                }
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->where('product_variant_id', $request->product_variant_id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;

                $availableStock = $variant ? $variant->currentStock : $product->currentStock;
                if ($newQuantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add more items. Only {$availableStock} items available"
                    ], 400);
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'product_variant_id' => $request->product_variant_id,
                    'quantity' => $request->quantity,
                ]);
            }

            DB::commit();

            $cart->load(['items.product.images', 'items.variant.size', 'items.variant.color']);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart' => [
                    'items_count' => $cart->items_count,
                    'subtotal' => (float) $cart->subtotal,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'order_number' => 'nullable|string'
        ]);

        try {
            if ($request->filled('order_number') && str_starts_with($itemId, 'order_')) {
                $realId = str_replace('order_', '', $itemId);

                $order = Order::with([
                    'items.product',
                    'items.variant.size',
                    'items.variant.color'
                ])
                    ->where('order_number', $request->order_number)
                    ->first();

                $orderItem = $order->items()->where('id', $realId)->first();

                $availableStock = $orderItem->variant
                    ? $orderItem->variant->currentStock
                    : $orderItem->product->currentStock;

                if ($request->quantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$availableStock} available"
                    ], 400);
                }

                if ($orderItem->quantity > $request->quantity) {
                    $quantity = $orderItem->quantity - $request->quantity;

                    if ($orderItem->product_variant_id) {
                        $variant = ProductVariant::find($orderItem->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock_in', $quantity);
                        }
                    } else {
                        $product = Product::find($orderItem->product_id);
                        if ($product) {
                            $product->increment('stock_in', $quantity);
                        }
                    }
                } else if ($orderItem->quantity < $request->quantity) {
                    $quantity = $request->quantity - $orderItem->quantity;
                    if ($orderItem->product_variant_id) {
                        $variant = ProductVariant::find($orderItem->product_variant_id);
                        if ($variant) {
                            $variant->decrement('stock_in', $quantity);
                        }
                    } else {
                        $product = Product::find($orderItem->product_id);
                        if ($product) {
                            $product->decrement('stock_in', $quantity);
                        }
                    }
                }

                $orderItem->quantity = $request->quantity;
                $orderItem->subtotal = $request->quantity * $orderItem->unit_price;
                $orderItem->save();
            } else {
                $cart = $this->getOrCreateCart($request->order_number);

                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('id', $itemId)
                    ->firstOrFail();

                $availableStock = $cartItem->variant
                    ? $cartItem->variant->currentStock
                    : $cartItem->product->currentStock;

                if ($request->quantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$availableStock} available"
                    ], 400);
                }

                $cartItem->quantity = $request->quantity;
                $cartItem->save();
            }

            return response()->json([
                'success' => true,
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeItem(Request $request, $itemId)
    {
        try {

            $orderNumber = $request->order_number ?? null;

            if ($orderNumber && str_starts_with($itemId, 'order_')) {

                $realId = (int) str_replace('order_', '', $itemId);

                $order = Order::where('order_number', $orderNumber)->firstOrFail();

                $orderItem = $order->items()->where('id', $realId)->firstOrFail();

                $quantity = $orderItem->quantity;

                if ($orderItem->product_variant_id) {
                    $variant = ProductVariant::find($orderItem->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock_in', $quantity);
                    }
                } else {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $product->increment('stock_in', $quantity);
                    }
                }

                $order->subtotal -= $orderItem->subtotal;
                $order->total -= $orderItem->total;
                $order->payable -= $orderItem->total;
                $order->save();

                $orderItem->delete();
            } else {

                $cart = Cart::where('is_pos', 1)
                    ->when($orderNumber, fn($q) => $q->where('order_number', $orderNumber))
                    ->when(!$orderNumber, fn($q) => $q->whereNull('order_number')->latest())
                    ->firstOrFail();

                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('id', $itemId)
                    ->firstOrFail();

                $cartItem->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
                'cart' => $this->getCartResponse($orderNumber)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateItemPrice(Request $request, $itemId)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'order_number' => 'nullable|string'
        ]);

        try {
            if ($request->filled('order_number') && str_starts_with($itemId, 'order_')) {

                $realId = str_replace('order_', '', $itemId);

                $order = Order::where('order_number', $request->order_number)->firstOrFail();

                $orderItem = $order->items()->where('id', $realId)->firstOrFail();

                $orderItem->unit_price = $request->price;
                $orderItem->subtotal = $request->price * $orderItem->quantity;
                $orderItem->save();
            } else {

                $cart = $this->getOrCreateCart($request->order_number);

                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('id', $itemId)
                    ->firstOrFail();

                $cartItem->item_unit_price = $request->price;
                $cartItem->item_total_price = $request->price * $cartItem->quantity;
                $cartItem->save();
            }

            return response()->json([
                'success' => true,
                'cart' => $this->getCartResponse($request->order_number)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCart()
    {
        try {

            $cart = $this->getOrCreateCart();

            $cart->items()->delete();
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPosOrders(Request $request)
    {
        $type = $request->type;

        $query = Order::where('is_pos', 1)
            ->with(['user', 'customer'])
            ->latest();

        if ($type === OrderStatus::DRAFT->value) {

            $query->where('status', OrderStatus::DRAFT);
        } else {

            $query->where('status', OrderStatus::DELIVERED)
                ->whereBetween('created_at', [
                    Carbon::today(),
                    Carbon::now()
                ]);
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->shipping_name
                    ?? $order->user?->name
                    ?? $order->customer?->name
                    ?? 'Guest',
                'total' => $order->total,
                'status' => $order->status,
            ];
        });

        return response()->json(['data' => $orders]);
    }

    public function posSales(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'employee'])
            ->where('is_pos', 1);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELLED,
                OrderStatus::DRAFT,
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        } elseif ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        } elseif ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $orders = $query->orderByDesc('created_at')->paginate(20);

        $statusCounts = [
            'delivered' => Order::where('is_pos', 1)
                ->where('status', OrderStatus::DELIVERED)
                ->count(),

            'cancelled' => Order::where('is_pos', 1)
                ->where('status', OrderStatus::CANCELLED)
                ->count(),

            'draft' => Order::where('is_pos', 1)
                ->where('status', OrderStatus::DRAFT)
                ->count(),
            'all' => Order::where('is_pos', 1)
                ->count(),
        ];

        return view('admin.pos.sales', compact('orders', 'statusCounts'));
    }

    public function saleShow(Request $request, $order_number)
    {
        $order = Order::with([
            'user',
            'items.product',
            'coupon',
            'statusHistories'
        ])->where('order_number', $order_number)->first();

        $source = $request->source;

        $refunds = SaleReturn::where('sale_id', $order->id)
            ->selectRaw('refund_method, SUM(refund_amount) as total')
            ->groupBy('refund_method')
            ->pluck('total', 'refund_method');

        $totalRefund = $refunds->sum();

        return view('admin.pos.sale_show', compact('order', 'source', 'refunds', 'totalRefund'));
    }

    public function saleDelete(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->items()->delete();

        activity_log(
            action: 'deleted',
            model: $order,
            description: 'Order deleted ',
        );

        $order->delete();

        toast_success('Sale deleted successfully!');

        return redirect()->route('admin.pos.sales.index');
    }

    private function getCartResponse($orderNumber = null)
    {
        $cart = $this->getOrCreateCart($orderNumber);

        $cart->load([
            'items.product',
            'items.variant.size',
            'items.variant.color'
        ]);

        $cartItems = $cart->items->map(function ($item) {

            $size = $item->variant?->size?->name;
            $color = $item->variant?->color?->name;

            $sku = $item->product_variant_id
                ? $item->variant?->sku
                : $item->product?->sku;

            return [
                'id' => $item->id,
                'source' => 'cart',
                'product_id' => $item->product_id,
                'sku' => $sku,
                'product_name' => $item->product->name ?? '',
                'product_image' => $item->product->thumbnail ?? asset('assets/images/default.png'),
                'variant_id' => $item->product_variant_id,
                'variant_name' => trim(($size ?? '') . ' - ' . ($color ?? ''), ' -') ?: 'Standard',
                'size' => $size,
                'color' => $color,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->unit_price,
                'compare_price' => (float) $item->product->compare_price,
                'stock' => (int) ($item->variant->stock ?? 0),
                'total_price' => (float) $item->total_price,
            ];
        });

        $orderItems = collect();

        if ($orderNumber) {
            $order = Order::with(['items.product.images', 'customer'])
                ->where('order_number', $orderNumber)
                ->where('is_pos', 1)
                ->first();

            if ($order) {
                $orderItems = $order->items->map(function ($item) {

                    $size = $item->variant?->size?->name;
                    $color = $item->variant?->color?->name;

                    return [
                        'id' => 'order_' . $item->id,
                        'source' => 'order',
                        'product_id' => $item->product_id,
                        'sku' => $item->product_variant_id
                            ? $item->variant?->sku
                            : $item->product?->sku,
                        'product_name' => $item->product->name ?? '',
                        'product_image' => $item->product->thumbnail ?? asset('assets/images/default.png'),
                        'variant_id' => $item->product_variant_id,
                        'variant_name' => trim(($size ?? '') . ' - ' . ($color ?? ''), ' -') ?: 'Standard',
                        'size' => $size,
                        'color' => $color,
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->unit_price,
                        'compare_price' => (float) $item->product->compare_price ?? 1700,
                        'stock' => (int) ($item->variant->stock),
                        'total_price' => (float) $item->subtotal,
                    ];
                });
            }
        }

        $items = collect($cartItems)
            ->merge($orderItems)
            ->values();

        $subtotal = $items->sum('total_price');
        $discount = 0;
        $tax = 0;

        return [
            'id' => $cart->id,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $tax - $discount,
            'items_count' => $items->count(),
            'total_items' => $items->sum('quantity'),
        ];
    }

    public function receipt($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('customer', 'items')->first();

        return view('admin.pos.receipt', compact('order'));
    }
}
