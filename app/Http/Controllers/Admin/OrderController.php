<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
            ->where('is_pos', 0)
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by order number, customer name, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->paginate(20)->appends($request->all());

        // Get counts for filters
        $statusCounts = [
            'all' => Order::where('is_pos', 0)->count(),
            'pending' => Order::where('is_pos', 0)->where('status', OrderStatus::PENDING)->count(),
            'confirmed' => Order::where('is_pos', 0)->where('status', OrderStatus::CONFIRMED)->count(),
            'shipped' => Order::where('is_pos', 0)->where('status', OrderStatus::SHIPPED)->count(),
            'delivered' => Order::where('is_pos', 0)->where('status', OrderStatus::DELIVERED)->count(),
            'cancelled' => Order::where('is_pos', 0)->where('status', OrderStatus::CANCELLED)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, $order_number)
    {
        $order = Order::with([
            'user',
            'items.product',
            'coupon',
            'statusHistories'
        ])->where('order_number',$order_number)->first();

        $source = $request->source;

        $refunds =SaleReturn::where('sale_id', $order->id)
            ->selectRaw('refund_method, SUM(refund_amount) as total')
            ->groupBy('refund_method')
            ->pluck('total', 'refund_method');

        $totalRefund = $refunds->sum();

        return view('admin.orders.show', compact('order', 'source','refunds','totalRefund'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = OrderStatus::from($request->status);

        // Update order status
        $order->update([
            'status' => $newStatus,
        ]);

        // Set timestamps based on status
        match ($newStatus) {
            OrderStatus::CONFIRMED => $order->update(['confirmed_at' => now()]),
            OrderStatus::SHIPPED => $order->update(['shipped_at' => now()]),
            OrderStatus::DELIVERED => $order->update(['delivered_at' => now()]),
            OrderStatus::CANCELLED => $order->update([
                'cancelled_at' => now(),
                'cancellation_reason' => $request->comment
            ]),
            default => null,
        };

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'comment' => $request->comment ?? "Status changed from {$oldStatus->label()} to {$newStatus->label()}",
            'updated_by' => Auth::id(),
        ]);

        toast_success('Order status updated successfully!');
        return back();
    }

    /**
     * Update tracking information.
     */
    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier' => 'required|string|max:100',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'tracking_number' => $request->tracking_number,
            'courier' => $request->courier,
        ]);

        // Add to history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => "Tracking info added: {$request->courier} - {$request->tracking_number}",
            'updated_by' => Auth::id(),
        ]);

        toast_success('Tracking information updated successfully!');
        return back();
    }

    /**
     * Update admin notes.
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['admin_notes' => $request->admin_notes]);

        toast_success('Admin notes updated successfully!');
        return back();
    }

    /**
     * Delete an order.
     */
    public function destroy(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->items()->delete();

        $order->delete();

        toast_success('Order deleted successfully!');

        return redirect()->route('admin.orders.index');
    }

    public function invoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('customer', 'items')->first();

        return view('admin.orders.invoice', compact('order'));
    }
}
