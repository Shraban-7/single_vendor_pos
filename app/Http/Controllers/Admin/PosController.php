<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'variants.size', 'variants.color', 'images'])
            ->where('is_active', true)
            ->get();

        $categories = Category::where('is_active', true)->get();

        return view('admin.pos.index', compact('products', 'categories'));
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

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Map payment method string to enum
            $paymentMethodEnum = match (strtolower($request->payment_method)) {
                'cash' => PaymentMethod::CASH,
                'card' => PaymentMethod::CARD,
                'mobile' => PaymentMethod::MOBILE_BANKING,
                default => PaymentMethod::CASH,
            };

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => null, // POS orders don't require user
                'shipping_name' => $request->customer_name ?? 'Walk-in Customer',
                'shipping_phone' => null,
                'shipping_email' => null,
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount ?? 0,
                'shipping_cost' => 0,
                'tax_amount' => 0,
                'total' => $request->total,
                'status' => OrderStatus::COMPLETED,
                'payment_method' => $paymentMethodEnum,
                'payment_status' => PaymentStatus::PAID,
                'notes' => 'POS Order',
                'paid_at' => now(),
            ]);

            // Create order items and update stock
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
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
                        $product->increment('sold_count', $item['quantity']);
                    }
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
}
