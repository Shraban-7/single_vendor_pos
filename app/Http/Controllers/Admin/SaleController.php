<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleStatusHistory;
use App\Models\SaleReturn;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'customer', 'items'])
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
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
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
            'all' => Sale::count(),
            'draft' => Sale::where('status', SaleStatus::DRAFT->value)->count(),
            'complted' => Sale::where('status', SaleStatus::COMPLETED->value)->count(),
        ];

        return view('admin.sales.index', compact('sales', 'statusCounts'));
    }

    public function show(Request $request, $order_number)
    {
        $sale = Sale::with([
            'user',
            'customer',
            'employee',
            'items.product',
            'statusHistories'
        ])->where('invoice_number', $order_number)->firstOrFail();

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

        $sale->updateStatus($newStatus, $request->comment, (string) Auth::id());

        toast_success('Sale status updated successfully!');
        return back();
    }

    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update(['notes' => $request->notes]);

        toast_success('Sale notes updated successfully!');
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
        $sale = Sale::with(['customer', 'employee', 'items.product', 'statusHistories'])
            ->where('invoice_number', $orderNumber)
            ->firstOrFail();

        return view('admin.sales.show', compact('sale'));
    }
}
