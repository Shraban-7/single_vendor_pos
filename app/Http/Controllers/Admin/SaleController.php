<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleStatusHistory;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items'])
            ->where('is_pos', 0)
            ->orderByDesc('created_at');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sales = $query->paginate(20)->appends($request->all());

        $statusCounts = [
            'all' => Sale::where('is_pos', 0)->count(),
            'pending' => Sale::where('is_pos', 0)->where('status', SaleStatus::PENDING)->count(),
            'confirmed' => Sale::where('is_pos', 0)->where('status', SaleStatus::CONFIRMED)->count(),
            'shipped' => Sale::where('is_pos', 0)->where('status', SaleStatus::SHIPPED)->count(),
            'delivered' => Sale::where('is_pos', 0)->where('status', SaleStatus::DELIVERED)->count(),
            'cancelled' => Sale::where('is_pos', 0)->where('status', SaleStatus::CANCELLED)->count(),
        ];

        return view('admin.sales.index', compact('sales', 'statusCounts'));
    }

    public function show(Request $request, $order_number)
    {
        $sale = Sale::with([
            'user',
            'items.product',
            'coupon',
            'statusHistories'
        ])->where('order_number', $order_number)->first();

        $source = $request->source;

        $refunds = SaleReturn::where('sale_id', $sale->id)
            ->selectRaw('refund_method, SUM(refund_amount) as total')
            ->groupBy('refund_method')
            ->pluck('total', 'refund_method');

        $totalRefund = $refunds->sum();

        return view('admin.sales.show', compact('sale', 'source', 'refunds', 'totalRefund'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        $sale = Sale::findOrFail($id);
        $oldStatus = $sale->status;
        $newStatus = SaleStatus::from($request->status);

        $sale->update([
            'status' => $newStatus,
        ]);

        match ($newStatus) {
            SaleStatus::CONFIRMED => $sale->update(['confirmed_at' => now()]),
            SaleStatus::SHIPPED => $sale->update(['shipped_at' => now()]),
            SaleStatus::DELIVERED => $sale->update(['delivered_at' => now()]),
            SaleStatus::CANCELLED => $sale->update([
                'cancelled_at' => now(),
                'cancellation_reason' => $request->comment
            ]),
            default => null,
        };

        SaleStatusHistory::create([
            'sale_id' => $sale->id,
            'status' => $newStatus,
            'comment' => $request->comment ?? "Status changed from {$oldStatus->label()} to {$newStatus->label()}",
            'updated_by' => Auth::id(),
        ]);

        toast_success('Sale status updated successfully!');
        return back();
    }

    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier' => 'required|string|max:100',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update([
            'tracking_number' => $request->tracking_number,
            'courier' => $request->courier,
        ]);

        SaleStatusHistory::create([
            'sale_id' => $sale->id,
            'status' => $sale->status,
            'comment' => "Tracking info added: {$request->courier} - {$request->tracking_number}",
            'updated_by' => Auth::id(),
        ]);

        toast_success('Tracking information updated successfully!');
        return back();
    }

    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update(['admin_notes' => $request->admin_notes]);

        toast_success('Admin notes updated successfully!');
        return back();
    }

    public function destroy(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $sale->items()->delete();

        $sale->delete();

        toast_success('Sale deleted successfully!');

        return redirect()->route('admin.sales.index');
    }

    public function invoice($orderNumber)
    {
        $sale = Sale::where('order_number', $orderNumber)->with('customer', 'items')->first();

        return view('admin.sales.invoice', compact('sale'));
    }
}
