<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary widgets, charts and reports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $filter = request('filter', 'today');

        $ranges = $this->getDateRanges($filter);
        $currentStart = $ranges['currentStart'];
        $currentEnd = $ranges['currentEnd'];
        $previousStart = $ranges['previousStart'];
        $previousEnd = $ranges['previousEnd'];

        $widgets = $this->getWidgetStats($currentStart, $currentEnd, $previousStart, $previousEnd);
        $products = $this->getProductStats();

        $chart = $this->getChartData($filter, $currentStart, $currentEnd);

        $categoryRevenue = $this->getCategoryRevenue($currentStart, $currentEnd);
        $paymentBreakdown = $this->getPaymentBreakdown($currentStart, $currentEnd);
        $orderStatusBreakdown = $this->getOrderStatusBreakdown($currentStart, $currentEnd);
        $topCustomers = $this->getTopCustomers($currentStart, $currentEnd);
        $customerSplit = $this->getNewVsReturningCustomers($currentStart, $currentEnd);
        $discountImpact = $this->getDiscountImpact($currentStart, $currentEnd);
        $stockReport = $this->getStockValueReport();
        $heatmap = $this->getHourlyHeatmap($filter, $currentStart, $currentEnd);
        $heatmapMax = collect($heatmap['data'])->flatten()->max() ?: 0;
        $recentOrders = $this->getRecentOrders();

        return view('admin.dashboard', compact(
            'widgets',
            'products',
            'chart',
            'categoryRevenue',
            'paymentBreakdown',
            'orderStatusBreakdown',
            'topCustomers',
            'customerSplit',
            'discountImpact',
            'stockReport',
            'heatmap',
            'heatmapMax',
            'recentOrders',
            'filter'
        ));
    }

    /**
     * Resolve the current and previous period date boundaries for the given filter.
     *
     * @param string $filter One of: today, this_week, this_month
     * @return array{currentStart: Carbon, currentEnd: Carbon, previousStart: Carbon, previousEnd: Carbon}
     */
    private function getDateRanges(string $filter): array
    {
        switch ($filter) {
            case 'this_week':
                return [
                    'currentStart' => now()->startOfWeek(Carbon::SUNDAY),
                    'currentEnd' => now()->endOfWeek(Carbon::SATURDAY),
                    'previousStart' => now()->subWeek()->startOfWeek(Carbon::SUNDAY),
                    'previousEnd' => now()->subWeek()->endOfWeek(Carbon::SATURDAY),
                ];

            case 'this_month':
                return [
                    'currentStart' => now()->startOfMonth(),
                    'currentEnd' => now()->endOfMonth(),
                    'previousStart' => now()->subMonth()->startOfMonth(),
                    'previousEnd' => now()->subMonth()->endOfMonth(),
                ];

            case 'today':
            default:
                return [
                    'currentStart' => today()->startOfDay(),
                    'currentEnd' => today()->endOfDay(),
                    'previousStart' => today()->subDay()->startOfDay(),
                    'previousEnd' => today()->subDay()->endOfDay(),
                ];
        }
    }

    /**
     * Calculate percentage change between a current and previous value.
     *
     * @param float|int $current
     * @param float|int $previous
     * @return float Percentage rounded to 1 decimal; 100 when previous is zero and current is positive.
     */
    private function calculatePercentage($current, $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Build the main revenue / refund time-series for the selected filter.
     *
     * Revenue is summed from sales.total_amount and refunds from sale_returns.returned_amount,
     * bucketed by hour (today), day (this_week / this_month).
     *
     * @param string $filter
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return array{labels: \Illuminate\Support\Collection, revenue: \Illuminate\Support\Collection, refunds: \Illuminate\Support\Collection, series: array}
     */
    private function getChartData(string $filter, Carbon $currentStart, Carbon $currentEnd): array
    {
        $buckets = [];

        if ($filter === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $start = $currentStart->copy()->addHours($i);
                $end = $start->copy()->endOfHour();
                $buckets[] = [
                    'label' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                    'from' => $start->toDateString() . ' ' . $start->format('H:i:s'),
                    'to' => $end->toDateString() . ' ' . $end->format('H:i:s'),
                ];
            }
        } elseif ($filter === 'this_week') {
            for ($i = 0; $i < 7; $i++) {
                $date = $currentStart->copy()->addDays($i);
                $buckets[] = [
                    'label' => $date->format('D'),
                    'from' => $date->startOfDay()->toDateString() . ' 00:00:00',
                    'to' => $date->endOfDay()->toDateString() . ' 23:59:59',
                ];
            }
        } else { // this_month
            $days = $currentEnd->day;
            for ($i = 1; $i <= $days; $i++) {
                $date = $currentStart->copy()->addDays($i - 1);
                $buckets[] = [
                    'label' => $date->format('d'),
                    'from' => $date->startOfDay()->toDateString() . ' 00:00:00',
                    'to' => $date->endOfDay()->toDateString() . ' 23:59:59',
                ];
            }
        }

        $series = [];
        foreach ($buckets as &$bucket) {
            $bucket['revenue'] = (float) Sale::whereBetween('sale_date', [$bucket['from'], $bucket['to']])->sum('total_amount');
            $bucket['refund'] = (float) SaleReturn::whereBetween('created_at', [$bucket['from'], $bucket['to']])->sum('refund_amount');
            $series[] = $bucket;
        }

        return [
            'labels' => collect($series)->pluck('label'),
            'revenue' => collect($series)->pluck('revenue'),
            'refunds' => collect($series)->pluck('refund'),
            'series' => $series,
        ];
    }

    /**
     * Aggregate the headline KPI widgets and their period-over-period growth.
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @param Carbon $previousStart
     * @param Carbon $previousEnd
     * @return array
     */
    private function getWidgetStats(Carbon $currentStart, Carbon $currentEnd, Carbon $previousStart, Carbon $previousEnd): array
    {
        $totalRevenue = (float) Sale::whereBetween('sale_date', [$currentStart, $currentEnd])->sum('total_amount');
        $previousRevenue = (float) Sale::whereBetween('sale_date', [$previousStart, $previousEnd])->sum('total_amount');

        $totalOrders = Sale::whereBetween('sale_date', [$currentStart, $currentEnd])->count();
        $previousOrders = Sale::whereBetween('sale_date', [$previousStart, $previousEnd])->count();

        $totalRefund = (float) SaleReturn::whereBetween('created_at', [$currentStart, $currentEnd])->sum('refund_amount');
        $previousRefund = (float) SaleReturn::whereBetween('created_at', [$previousStart, $previousEnd])->sum('refund_amount');

        $totalCustomers = Customer::whereBetween('created_at', [$currentStart, $currentEnd])->count()
            + User::where('role', 'customer')->whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previousCustomers = Customer::whereBetween('created_at', [$previousStart, $previousEnd])->count()
            + User::where('role', 'customer')->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $pendingOrders = Sale::where('status', SaleStatus::PENDING)->count();
        $todayOrders = Sale::whereDate('sale_date', today())->count();
        $todayRevenue = (float) Sale::whereDate('sale_date', today())->sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'totalRevenuePercentage' => $this->calculatePercentage($totalRevenue, $previousRevenue),
            'totalOrders' => $totalOrders,
            'totalOrdersPercentage' => $this->calculatePercentage($totalOrders, $previousOrders),
            'totalCustomers' => $totalCustomers,
            'totalCustomersPercentage' => $this->calculatePercentage($totalCustomers, $previousCustomers),
            'totalRefund' => $totalRefund,
            'totalRefundPercentage' => $this->calculatePercentage($totalRefund, $previousRefund),
            'pendingOrders' => $pendingOrders,
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'outOfStock' => Product::where('stock_quantity', '<=', 0)->count(),
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'avgOrderValue' => $avgOrderValue,
        ];
    }

    /**
     * Gather product-related statistics: top sellers, low/out-of-stock counts and totals.
     *
     * Uses query-level aggregation (groupBy / whereRaw) instead of loading all products.
     *
     * @return array{topProducts: \Illuminate\Support\Collection, lowStockCount: int, lowStockProducts: \Illuminate\Support\Collection, outOfStock: int, totalProducts: int, totalCategories: int, stockValue: float}
     */
    private function getProductStats(): array
    {
        $topProducts = SaleItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as sales'), DB::raw('SUM(subtotal) as revenue'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->take(5)
            ->with('product:id,name,image')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product?->name,
                    'image' => $item->product?->thumbnail,
                    'sales' => (int) $item->sales,
                    'revenue' => (float) $item->revenue,
                ];
            });

        $lowStockProducts = Product::query()
            ->select('id', 'name', 'image', 'stock_quantity', 'stock_alert_quantity')
            ->whereColumn('stock_quantity', '<=', 'stock_alert_quantity')
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        $lowStockCount = $lowStockProducts->count();
        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $stockValue = (float) Product::where('stock_quantity', '>', 0)
            ->selectRaw('SUM(stock_quantity * cost_price) as value')
            ->value('value');

        return [
            'topProducts' => $topProducts,
            'lowStockCount' => $lowStockCount,
            'lowStockProducts' => $lowStockProducts,
            'outOfStock' => $outOfStock,
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'stockValue' => $stockValue,
        ];
    }

    /**
     * Revenue grouped by product category for the selected period (joined through sale_items → products).
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return \Illuminate\Support\Collection List of ['category' => string, 'revenue' => float, 'quantity' => int]
     */
    private function getCategoryRevenue(Carbon $currentStart, Carbon $currentEnd): \Illuminate\Support\Collection
    {
        $rows = SaleItem::query()
            ->select('products.category_id', DB::raw('SUM(sale_items.subtotal) as revenue'), DB::raw('SUM(sale_items.quantity) as quantity'))
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.sale_date', [$currentStart, $currentEnd])
            ->groupBy('products.category_id')
            ->orderByDesc('revenue')
            ->get();

        $categoryNames = Category::pluck('name', 'id');

        return $rows->map(function ($row) use ($categoryNames) {
            return [
                'category' => $categoryNames->get($row->category_id) ?? 'Uncategorized',
                'revenue' => (float) $row->revenue,
                'quantity' => (int) $row->quantity,
            ];
        });
    }

    /**
     * Sales split by payment method for the period (cash / card / mobile etc.).
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return \Illuminate\Support\Collection List of ['method' => string, 'count' => int, 'total' => float]
     */
    private function getPaymentBreakdown(Carbon $currentStart, Carbon $currentEnd): \Illuminate\Support\Collection
    {
        return Sale::query()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('sale_date', [$currentStart, $currentEnd])
            ->groupBy('payment_method')
            ->get()
            ->map(function ($row) {
                return [
                    'method' => $row->payment_method ?? 'Unknown',
                    'count' => (int) $row->count,
                    'total' => (float) $row->total,
                ];
            });
    }

    /**
     * Count of orders grouped by status for the period.
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return \Illuminate\Support\Collection List of ['status' => string, 'count' => int]
     */
    private function getOrderStatusBreakdown(Carbon $currentStart, Carbon $currentEnd): \Illuminate\Support\Collection
    {
        return Sale::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('sale_date', [$currentStart, $currentEnd])
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                return [
                    'status' => $row->status,
                    'count' => (int) $row->count,
                ];
            });
    }

    /**
     * Top 5 customers by total spend in the selected period.
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return \Illuminate\Support\Collection List of ['name' => string, 'orders' => int, 'spend' => float]
     */
    private function getTopCustomers(Carbon $currentStart, Carbon $currentEnd): \Illuminate\Support\Collection
    {
        return Sale::query()
            ->select('customer_id', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total_amount) as spend'))
            ->whereBetween('sale_date', [$currentStart, $currentEnd])
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('spend')
            ->take(5)
            ->with('customer:id,name,phone')
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->customer?->name ?? 'Walk-In',
                    'orders' => (int) $row->orders,
                    'spend' => (float) $row->spend,
                ];
            });
    }

    /**
     * Split of customers into first-time (new) vs repeat (returning) within the period.
     *
     * Returning = placed a sale in the period AND had a sale before the period.
     * New = all their sales fall within the period.
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return array{new: int, returning: int}
     */
    private function getNewVsReturningCustomers(Carbon $currentStart, Carbon $currentEnd): array
    {
        $periodCustomerIds = Sale::whereBetween('sale_date', [$currentStart, $currentEnd])
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $returning = Sale::whereBetween('sale_date', [$currentStart, $currentEnd])
            ->whereNotNull('customer_id')
            ->whereIn('customer_id', function ($query) use ($currentStart) {
                $query->select('customer_id')
                    ->from('sales')
                    ->where('sale_date', '<', $currentStart)
                    ->whereNotNull('customer_id');
            })
            ->distinct()
            ->count('customer_id');

        $new = max(0, $periodCustomerIds->count() - $returning);

        return [
            'new' => $new,
            'returning' => $returning,
        ];
    }

    /**
     * Discount impact: total sale-level + item-level discounts vs gross revenue.
     *
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return array{discountGiven: float, grossRevenue: float, percentage: float}
     */
    private function getDiscountImpact(Carbon $currentStart, Carbon $currentEnd): array
    {
        $saleDiscount = (float) Sale::whereBetween('sale_date', [$currentStart, $currentEnd])->sum('discount_amount');
        $itemDiscount = (float) SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$currentStart, $currentEnd])
            ->sum('sale_items.discount_amount');

        $grossRevenue = (float) Sale::whereBetween('sale_date', [$currentStart, $currentEnd])->sum('total_amount');
        $discountGiven = $saleDiscount + $itemDiscount;
        $percentage = $grossRevenue > 0 ? round(($discountGiven / $grossRevenue) * 100, 1) : 0;

        $discountCount = Sale::whereBetween('sale_date', [$currentStart, $currentEnd])
            ->where(function ($q) {
                $q->where('discount_amount', '>', 0)
                  ->orWhere('discount_type', '!=', 'none');
            })
            ->count();

        return [
            'discountGiven' => $discountGiven,
            'grossRevenue' => $grossRevenue,
            'percentage' => $percentage,
            'discountCount' => $discountCount,
        ];
    }

    /**
     * Inventory value report: total stock value plus low / out-of-stock counts.
     *
     * @return array{stockValue: float, lowStock: int, outOfStock: int}
     */
    private function getStockValueReport(): array
    {
        $stockValue = (float) Product::where('stock_quantity', '>', 0)
            ->selectRaw('SUM(stock_quantity * cost_price) as value')
            ->value('value');

        return [
            'stockValue' => $stockValue,
            'lowStock' => Product::whereColumn('stock_quantity', '<=', 'stock_alert_quantity')
                ->where('stock_quantity', '>', 0)->count(),
            'outOfStock' => Product::where('stock_quantity', '<=', 0)->count(),
        ];
    }

    /**
     * Build an hourly × day-of-week revenue heatmap for the period.
     *
     * Produces a 7 (days) × 24 (hours) grid for this_week / this_month filters,
     * and a flat 24-bucket series for the today filter.
     *
     * @param string $filter
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @return array{days: array, hours: array, data: array, flat: \Illuminate\Support\Collection}
     */
    private function getHourlyHeatmap(string $filter, Carbon $currentStart, Carbon $currentEnd): array
    {
        $hours = collect(range(0, 23));
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        if ($filter === 'today') {
            $flat = collect(range(0, 23))->map(function ($hour) use ($currentStart) {
                $from = $currentStart->copy()->addHours($hour)->startOfHour();
                $to = $from->copy()->endOfHour();
                return (float) Sale::whereBetween('sale_date', [
                    $from->toDateString() . ' 00:00:00',
                    $to->toDateString() . ' 23:59:59',
                ])->whereTime('sale_date', '>=', $from->format('H:i:s'))
                    ->whereTime('sale_date', '<=', $to->format('H:i:s'))
                    ->sum('total_amount');
            });

            return ['days' => $days, 'hours' => $hours->all(), 'data' => [], 'flat' => $flat];
        }

        // Aggregate revenue by (day-of-week, hour) for the period.
        $rows = Sale::query()
            ->select(
                DB::raw('DAYOFWEEK(sale_date) as dow'),
                DB::raw('HOUR(sale_date) as houravg'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$currentStart, $currentEnd])
            ->groupBy('dow', 'houravg')
            ->get();

        // DAYOFWEEK: 1=Sun ... 7=Sat -> map to 0..6 index
        $grid = [];
        foreach ($rows as $row) {
            $dayIdx = ($row->dow - 1) % 7;
            $grid[$dayIdx][$row->houravg] = (float) $row->total;
        }

        return ['days' => $days, 'hours' => $hours->all(), 'data' => $grid, 'flat' => collect()];
    }

    /**
     * Latest sales for the recent-sales table.
     *
     * @return \Illuminate\Support\Collection List of ['id','invoice_number','customer_name','sale_date','total_amount','status']
     */
    private function getRecentOrders(int $limit = 8): \Illuminate\Support\Collection
    {
        return Sale::query()
            ->select('id', 'invoice_number', 'customer_id', 'sale_date', 'total_amount', 'status')
            ->with('customer:id,name')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->take($limit)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => $sale->customer?->name ?? 'Walk-In',
                    'sale_date' => $sale->sale_date->format('M d, Y'),
                    'total_amount' => (float) $sale->total_amount,
                    'status' => $sale->status instanceof \App\Enums\SaleStatus
                        ? $sale->status->value
                        : (string) $sale->status,
                ];
            });
    }
}
