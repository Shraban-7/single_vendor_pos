<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    // Public order tracking (no login required)
    public function trackOrder(Request $request)
    {
        if ($request->has('order_number') || $request->has('phone')) {
            $request->validate([
                'order_number' => 'required_without:phone',
                'phone' => 'required_without:order_number',
            ], [
                'order_number.required_without' => 'Please provide either order number or phone number.',
                'phone.required_without' => 'Please provide either order number or phone number.',
            ]);

            $query = Order::query()->with(['items.product', 'statusHistories']);

            if ($request->filled('order_number') && $request->filled('phone')) {
                // Both provided - search with both for extra security
                $query->where('order_number', $request->order_number)
                    ->where('shipping_phone', $request->phone);
            } elseif ($request->filled('order_number')) {
                // Only order number
                $query->where('order_number', $request->order_number);
            } else {
                // Only phone number
                $query->where('shipping_phone', $request->phone);
            }

            $order = $query->first();

            if (!$order) {
                return back()
                    ->withInput()
                    ->with('error', 'Order not found. Please check your order number or phone number and try again.');
            }

            return view('orders.track', compact('order'));
        }
        return view('orders.track');
    }
}
