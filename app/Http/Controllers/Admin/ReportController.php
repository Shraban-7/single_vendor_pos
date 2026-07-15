<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Expense;
use App\Models\SaleReturn;
use App\Models\ExchangeItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $salesQuery = $user->sales()->whereBetween('sale_date', [$fromDate, $toDate]);

        $thisMonthSaleAmount = $user->sales()->whereYear('sale_date', now()->year)->whereMonth('sale_date', now()->month)->sum('total_amount');
        $lastMonthSaleAmount = $user->sales()->whereYear('sale_date', now()->subMonth()->year)->whereMonth('sale_date', now()->subMonth()->month)->sum('total_amount');
        $growthPercentage = $lastMonthSaleAmount == 0 ? 100 : (($thisMonthSaleAmount - $lastMonthSaleAmount) / abs($lastMonthSaleAmount)) * 100;

        $chartData = $user->sales()
            ->whereBetween('sale_date', [now()->subDays(29)->toDateString(), now()->toDateString()])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => ['date' => Carbon::parse($item->date)->format('d M'), 'amount' => (float) $item->amount]);

        $saleIds = (clone $salesQuery)->pluck('id');
        $daysWithSales = (clone $salesQuery)->selectRaw('COUNT(DISTINCT DATE(sale_date)) as active_days')->value('active_days') ?? 0;
        $totalPeriodAmount = (clone $salesQuery)->sum('total_amount');

        $data = [
            'sale_summary' => [
                'this_month' => money($thisMonthSaleAmount),
                'growth_percentage' => round($growthPercentage, 2),
                'display_period' => Carbon::parse($fromDate)->format('F Y'),
            ],
            'sales_chart' => $chartData,
            'cash_sale' => [
                'amount' => money((clone $salesQuery)->where('payment_method', 'cash')->sum('total_amount')),
                'count' => (clone $salesQuery)->where('payment_method', 'cash')->count(),
            ],
            'online_sale' => [
                'amount' => money((clone $salesQuery)->where('payment_method', '!=', 'cash')->sum('total_amount')),
                'count' => (clone $salesQuery)->where('payment_method', '!=', 'cash')->count(),
            ],
            'due_sale' => [
                'amount' => money((clone $salesQuery)->where('due_amount', '>', 0)->sum('total_amount')),
                'count' => (clone $salesQuery)->where('due_amount', '>', 0)->count(),
            ],
            'average_sale' => [
                'amount' => money($daysWithSales > 0 ? ($totalPeriodAmount / $daysWithSales) : 0),
                'count' => $daysWithSales,
            ],
            'top_selling_items' => SaleItem::query()
                ->select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_sales'))
                ->whereIn('sale_id', $saleIds)
                ->groupBy('product_name')
                ->orderByDesc('total_quantity')
                ->take(10)
                ->get(),
            'daily_sales' => (clone $salesQuery)
                ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as amount, COUNT(id) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.reports.sales', compact('data', 'month'));
    }

    public function purchaseReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $purchasesQuery = $user->purchases()->whereBetween('purchase_date', [$fromDate, $toDate]);
        $purchaseIds = (clone $purchasesQuery)->pluck('id');

        $data = [
            'paid_purchase' => [
                'amount' => money((clone $purchasesQuery)->where('payment_status', 'paid')->sum('subtotal')),
                'count' => (clone $purchasesQuery)->where('payment_status', 'paid')->count(),
            ],
            'unpaid_purchase' => [
                'amount' => money((clone $purchasesQuery)->where('payment_status', 'unpaid')->sum('due_amount')),
                'count' => (clone $purchasesQuery)->where('payment_status', 'unpaid')->count(),
            ],
            'supplier_count' => [
                'total' => Supplier::where('user_id', $user->id)->count(),
                'active' => (clone $purchasesQuery)->distinct('supplier_id')->count('supplier_id'),
            ],
            'top_suppliers' => (clone $purchasesQuery)
                ->selectRaw('supplier_id, COUNT(id) as purchase_count, SUM(subtotal) as total_purchases')
                ->whereNotNull('supplier_id')
                ->groupBy('supplier_id')
                ->orderByDesc('total_purchases')
                ->take(10)
                ->get()
                ->map(fn($item) => [
                    'name' => Supplier::find($item->supplier_id)?->name ?? 'Unknown',
                    'count' => $item->purchase_count,
                    'amount' => money($item->total_purchases),
                ]),
            'top_purchasing_items' => PurchaseItem::query()
                ->select('name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(unit_price * quantity) as total_purchases'))
                ->whereIn('purchase_id', $purchaseIds)
                ->groupBy('name')
                ->orderByDesc('total_quantity')
                ->take(10)
                ->get(),
        ];

        return view('admin.reports.purchases', compact('data', 'month'));
    }

    public function profitLossReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $totalRevenue = $user->sales()->whereBetween('sale_date', [$fromDate, $toDate])->sum('total_amount');
        $purchaseAmount = $user->purchases()->whereBetween('purchase_date', [$fromDate, $toDate])->sum('subtotal');
        $otherExpensesAmount = $user->expenses()->whereBetween('expense_date', [$fromDate, $toDate])->sum('amount');
        $totalExpenses = $purchaseAmount + $otherExpensesAmount;
        $netProfit = $totalRevenue - $totalExpenses;

        $expenseBreakdown = $user->expenses()
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->selectRaw('category_id, SUM(amount) as total_amount')
            ->groupBy('category_id')
            ->get()
            ->map(fn($expense) => [
                'category' => $expense->category?->name ?? 'Uncategorized',
                'amount' => money($expense->total_amount),
                'raw_amount' => $expense->total_amount,
            ])
            ->push(['category' => 'Goods Purchases', 'amount' => money($purchaseAmount), 'raw_amount' => $purchaseAmount])
            ->sortByDesc('raw_amount')
            ->map(fn($item) => ['category' => $item['category'], 'amount' => $item['amount']])
            ->values();

        $data = [
            'net_profit' => money($netProfit),
            'profit_margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0,
            'total_sales' => money($totalRevenue),
            'total_expenses' => money($totalExpenses),
            'expense_breakdown' => $expenseBreakdown,
            'sales_graph' => $user->sales()
                ->whereBetween('sale_date', [now()->startOfYear(), now()->endOfMonth()])
                ->selectRaw('MONTH(sale_date) as month, SUM(total_amount) as total_amount')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($item) => [
                    'month' => Carbon::create()->month($item->month)->format('M'),
                    'amount' => (float) $item->total_amount,
                ]),
        ];

        return view('admin.reports.profit-loss', compact('data', 'month', 'netProfit'));
    }

    public function stockReport()
    {
        $user = Auth::user();
        $products = Product::with(['unit', 'category'])
            ->where('user_id', $user->id)
            ->get(['id', 'name', 'sku', 'category_id', 'stock_out', 'stock_quantity', 'stock_alert_quantity', 'cost_price', 'unit_id']);

        $totalStockValue = $products->sum(fn($p) => $p->stock_quantity * $p->cost_price);

        $inStock = $products->filter(fn($p) => $p->stock_quantity > ($p->stock_alert_quantity ?? 0));
        $lowStock = $products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= ($p->stock_alert_quantity ?? 0));
        $outOfStock = $products->filter(fn($p) => $p->stock_quantity <= 0);

        $data = [
            'stock_report' => [
                'product_count' => $products->count(),
                'category_count' => $products->whereNotNull('category_id')->pluck('category_id')->unique()->count(),
                'stock_value' => money($totalStockValue),
            ],
            'in_stock' => ['product_count' => $inStock->count(), 'stock_count' => $inStock->sum('stock_quantity')],
            'low_stock' => ['product_count' => $lowStock->count(), 'stock_count' => $lowStock->sum('stock_quantity')],
            'out_of_stock' => ['product_count' => $outOfStock->count(), 'stock_count' => 0],
            'stock_alert_list' => $lowStock->merge($outOfStock)->sortBy('stock_quantity')->take(20)->map(fn($p) => [
                'product_name' => $p->name,
                'sku' => $p->sku ?? '-',
                'stock_quantity' => $p->stock_quantity . ' ' . ($p->unit?->short_name ?? ''),
                'stock_alert_quantity' => $p->stock_alert_quantity ?? 0,
            ])->values(),
            'category_stock_breakdown' => $products->groupBy('category_id')->map(function ($group) {
                $first = $group->first();
                return [
                    'name' => $first->category?->name ?? 'Uncategorized',
                    'product_count' => $group->count(),
                    'stock_count' => $group->sum('stock_quantity'),
                    'stock_value' => money($group->sum(fn($p) => $p->stock_quantity * $p->cost_price)),
                ];
            })->sortByDesc('stock_value')->values(),
        ];

        return view('admin.reports.stock', compact('data'));
    }

    public function customerReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $salesMetrics = $user->sales()
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->selectRaw('COUNT(id) as total_invoice_count, SUM(COALESCE(total_amount, 0)) as total_sales_amount, SUM(COALESCE(due_amount, 0)) as total_due_amount')
            ->first();

        $data = [
            'customer_summary' => [
                'total' => Customer::count(),
                'new' => Customer::whereBetween('created_at', [$fromDate, $toDate])->count(),
                'active' => Customer::whereHas('sales', fn($q) => $q->whereBetween('sale_date', [$fromDate, $toDate]))->count(),
            ],
            'total_sales' => [
                'amount' => money($salesMetrics->total_sales_amount),
                'count' => $salesMetrics->total_invoice_count,
            ],
            'total_due' => [
                'amount' => money($salesMetrics->total_due_amount),
            ],
            'top_customers' => Customer::whereHas('sales', fn($q) => $q->whereBetween('sale_date', [$fromDate, $toDate]))
                ->withSum(['sales' => fn($q) => $q->whereBetween('sale_date', [$fromDate, $toDate])], 'total_amount')
                ->withCount(['sales' => fn($q) => $q->whereBetween('sale_date', [$fromDate, $toDate])])
                ->orderByDesc('sales_sum_total_amount')
                ->take(10)
                ->get()
                ->map(fn($c) => [
                    'name' => $c->name,
                    'phone' => $c->phone ?? '-',
                    'total_purchase' => money($c->sales_sum_total_amount ?? 0),
                    'purchase_count' => $c->sales_count ?? 0,
                ]),
        ];

        return view('admin.reports.customers', compact('data', 'month'));
    }

    public function supplierReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $purchaseMetrics = $user->purchases()
            ->whereBetween('purchase_date', [$fromDate, $toDate])
            ->selectRaw('COUNT(id) as total_purchase_count, SUM(COALESCE(subtotal, 0)) as total_purchase_amount, SUM(COALESCE(due_amount, 0)) as total_due_amount')
            ->first();

        $data = [
            'supplier_summary' => [
                'total' => Supplier::where('user_id', $user->id)->count(),
                'new' => Supplier::where('user_id', $user->id)->whereBetween('created_at', [$fromDate, $toDate])->count(),
                'active' => Supplier::where('user_id', $user->id)->whereHas('purchases', fn($q) => $q->whereBetween('purchase_date', [$fromDate, $toDate]))->count(),
            ],
            'total_purchases' => [
                'amount' => money($purchaseMetrics->total_purchase_amount),
                'count' => $purchaseMetrics->total_purchase_count,
            ],
            'total_due' => [
                'amount' => money($purchaseMetrics->total_due_amount),
            ],
            'top_suppliers' => Supplier::where('user_id', $user->id)
                ->whereHas('purchases', fn($q) => $q->whereBetween('purchase_date', [$fromDate, $toDate]))
                ->withSum(['purchases' => fn($q) => $q->whereBetween('purchase_date', [$fromDate, $toDate])], 'subtotal')
                ->withCount(['purchases' => fn($q) => $q->whereBetween('purchase_date', [$fromDate, $toDate])])
                ->orderByDesc('purchases_sum_subtotal')
                ->take(10)
                ->get()
                ->map(fn($s) => [
                    'name' => $s->name,
                    'phone' => $s->phone ?? '-',
                    'total_purchase' => money($s->purchases_sum_subtotal ?? 0),
                    'purchase_count' => $s->purchases_count ?? 0,
                ]),
        ];

        return view('admin.reports.suppliers', compact('data', 'month'));
    }

    public function expenseReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', now()->format('Y-m'));
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $toDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $currentAmount = $user->expenses()->whereBetween('expense_date', [$fromDate, $toDate])->sum('amount');
        $lastMonthAmount = $user->expenses()->whereBetween('expense_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('amount');
        $growthRate = $lastMonthAmount > 0 ? (($currentAmount - $lastMonthAmount) / $lastMonthAmount) * 100 : 0;

        $data = [
            'expense_summary' => [
                'total' => money($currentAmount),
                'transaction_count' => $user->expenses()->whereBetween('expense_date', [$fromDate, $toDate])->count(),
                'growth_percentage' => round($growthRate, 1),
            ],
            'category_expenses' => $user->expenses()
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->selectRaw('category_id, SUM(amount) as category_total')
                ->groupBy('category_id')
                ->get()
                ->map(fn($item) => [
                    'name' => $item->category?->name ?? 'Uncategorized',
                    'amount' => money($item->category_total),
                    'percentage' => $currentAmount > 0 ? round(($item->category_total / $currentAmount) * 100, 1) : 0,
                ])
                ->sortByDesc('percentage')
                ->values(),
            'recent_expenses' => $user->expenses()
                ->with('category')
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->orderByDesc('expense_date')
                ->take(5)
                ->get()
                ->map(fn($e) => [
                    'title' => $e->title,
                    'date' => Carbon::parse($e->expense_date)->format('d M Y'),
                    'category' => $e->category?->name ?? 'Uncategorized',
                    'amount' => money($e->amount),
                ]),
        ];

        return view('admin.reports.expenses', compact('data', 'month'));
    }
}
