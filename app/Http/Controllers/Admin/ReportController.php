<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\District;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function overview(Request $request)
    {
        $filter = $request->get('range', 'daily');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($filter !== 'custom') {

            $dates = $this->getDateRange($filter);

            $currentStart = $dates['currentStart'];
            $currentEnd = $dates['currentEnd'];

            $lastStart = $dates['lastStart'];
            $lastEnd = $dates['lastEnd'];

        } elseif ($filter === 'custom') {

            $currentStart = Carbon::parse($dateFrom)->startOfDay();
            $currentEnd = Carbon::parse($dateTo)->endOfDay();

            $days = Carbon::parse($dateFrom)
                ->diffInDays($dateTo) + 1;

            $lastEnd = Carbon::parse($dateFrom)->subDay()->endOfDay();

            $lastStart = $lastEnd->copy()
                ->subDays($days - 1)
                ->startOfDay();
        }

        $currentSales = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('total');

        $currentOrders = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();

        $currentAov = $currentOrders > 0
            ? $currentSales / $currentOrders
            : 0;

        $currentOrderItems = OrderItem::whereHas('order', function ($query) use ($currentStart, $currentEnd) {
            $query->whereBetween('created_at', [$currentStart, $currentEnd]);
        })
            ->with('product:id,cost_price')
            ->get();

        $currentCost = $currentOrderItems->sum(function ($item) {
            return ($item->product->cost_price ?? 0) * $item->quantity;
        });

        $currentProfit = $currentSales - $currentCost;

        $currentStock = Product::get()
            ->sum('totalStock');

        if ($lastStart && $lastEnd) {

            $lastSales = Order::whereBetween('created_at', [$lastStart, $lastEnd])
                ->sum('total');

            $lastOrders = Order::whereBetween('created_at', [$lastStart, $lastEnd])
                ->count();

            $lastAov = $lastOrders > 0
                ? $lastSales / $lastOrders
                : 0;

            $lastOrderItems = OrderItem::whereHas('order', function ($query) use ($lastStart, $lastEnd) {
                $query->whereBetween('created_at', [$lastStart, $lastEnd]);
            })
                ->with('product:id,cost_price')
                ->get();

            $lastCost = $lastOrderItems->sum(function ($item) {
                return ($item->product->cost_price ?? 0) * $item->quantity;
            });

            $lastProfit = $lastSales - $lastCost;

            $lastStock = Product::get()
                ->sum('totalStock');

        } else {

            $lastSales = 0;
            $lastOrders = 0;
            $lastAov = 0;
            $lastProfit = 0;
            $lastStock = 0;
        }

        $growth = function ($current, $last) {

            if ($last == 0) {
                return 0;
            }

            return round((($current - $last) / $last) * 100, 2);
        };

        $calculateMetrics = [
            'totalSales' => $currentSales,
            'totalOrders' => $currentOrders,
            'aov' => $currentAov,
            'netProfit' => $currentProfit,
            'totalStock' => $currentStock,
            'salesGrowth' => $growth($currentSales, $lastSales),
            'ordersGrowth' => $growth($currentOrders, $lastOrders),
            'aovGrowth' => $growth($currentAov, $lastAov),
            'profitGrowth' => $growth($currentProfit, $lastProfit),
            'stockGrowth' => $growth($currentStock, $lastStock),
        ];

        switch ($filter) {
            case 'daily':
                $trend = Order::whereBetween('created_at', [$currentStart, $currentEnd])
                    ->selectRaw('HOUR(created_at) as label, SUM(total) as revenue')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;
            case 'weekly':
                $trend = Order::whereBetween('created_at', [$currentStart, $currentEnd])
                    ->selectRaw('DAYNAME(created_at) as label, SUM(total) as revenue')
                    ->groupBy('label')
                    ->orderByRaw('MIN(created_at)')
                    ->get();
                break;
            case 'monthly':
                $trend = Order::whereBetween('created_at', [$currentStart, $currentEnd])
                    ->selectRaw('DAY(created_at) as label, SUM(total) as revenue')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;
            case 'yearly':
                $trend = Order::whereBetween('created_at', [$currentStart, $currentEnd])
                    ->selectRaw('MONTHNAME(created_at) as label, SUM(total) as revenue')
                    ->groupBy('label')
                    ->orderByRaw('MIN(created_at)')
                    ->get();
                break;
            case 'custom':
            default:
                $trend = Order::whereBetween('created_at', [$currentStart, $currentEnd])
                    ->selectRaw('DATE(created_at) as label, SUM(total) as revenue')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get();
                break;
        }

        $revenueTrend = [
            'labels' => $trend->pluck('label'),
            'values' => $trend->pluck('revenue'),
        ];

        $start = $currentStart;
        $end = $currentEnd;

        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

        $totalItems = OrderItem::whereHas('order', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })->count();

        $returnItemsQuery = SaleReturnItem::whereHas('saleReturn', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        });

        $returnedItems = (clone $returnItemsQuery)->count();

        $totalReturnedQty = (clone $returnItemsQuery)->sum('quantity');

        $returnStats = SaleReturn::whereBetween('created_at', [$start, $end]);
        $totalRefundAmount = (clone $returnStats)->sum('refund_amount');
        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

        $ordersWithReturns = SaleReturn::whereBetween('created_at', [$start, $end])->count();

        $fullyReturnedOrders = SaleReturn::whereBetween('created_at', [$start, $end])
            ->select('sale_id')
            ->groupBy('sale_id')
            ->havingRaw('SUM(refund_amount) >= (SELECT total FROM orders WHERE orders.id = sale_returns.sale_id)')
            ->count();

        $partialReturns = $ordersWithReturns - $fullyReturnedOrders;

        $successfulOrders = $totalOrders - $ordersWithReturns;

        $refundRatePercent = $totalOrders > 0
            ? round($ordersWithReturns / $totalOrders * 100, 2)
            : 0;

        $ordersReturnsChart = [
            'labels' => ['Successful Orders', 'Partial Returns', 'Full Returns'],
            'data' => [$successfulOrders, $partialReturns, $fullyReturnedOrders],
            'colors' => ['#10B981', '#F59E0B', '#EF4444'],
            'total_orders' => $totalOrders,
            'total_returns' => $ordersWithReturns,
            'return_rate' => $totalOrders > 0 ? round($ordersWithReturns / $totalOrders * 100, 2) : 0,
        ];

        $chartData = [
            'orders' => $totalOrders,
            'returns' => $ordersWithReturns,
            'full_returns' => $fullyReturnedOrders,
            'partial_returns' => $partialReturns,
            'total_items' => $totalItems,
            'returned_items' => $returnedItems,
            'return_rate_percent' => $refundRatePercent,
            'item_return_rate_percent' => $totalItems > 0
                ? round($returnedItems / $totalItems * 100, 2)
                : 0,
            'total_refund_amount' => $totalRefundAmount,
            'revenueTrend' => $revenueTrend
        ];

        $orders = Order::whereBetween('created_at', [$currentStart, $currentEnd]);

        $totalOrders = $orders->count();



        $bestSalesDay = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->selectRaw('DATE(created_at) as orderDate, SUM(total) as totalSales')
            ->groupBy('orderDate')
            ->orderByDesc('totalSales')
            ->first();

        $allPreviousCustomerIds = Order::where('created_at', '<', $currentStart)
            ->selectRaw('COALESCE(user_id, customer_id) as customer')
            ->pluck('customer')
            ->unique()
            ->toArray();

        $returningOrdersCount = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->whereIn(
                DB::raw('COALESCE(user_id, customer_id)'),
                $allPreviousCustomerIds
            )
            ->count();

        $returningCustomersPercent = $currentOrders > 0
            ? round(($returningOrdersCount / $currentOrders) * 100, 2)
            : 0;

        $quickFacts = [
            'totalOrders' => $currentOrders,

            'returningCustomersPercent' => $returningCustomersPercent,

            'refundRate' => $refundRatePercent,

            'bestSalesDay' => $bestSalesDay
                ? Carbon::parse($bestSalesDay->orderDate)->format('M d')
                . ' (' . money($bestSalesDay->totalSales) . ')'
                : null,
        ];

        $topProductsRaw = OrderItem::whereHas('order', function ($query) use ($currentStart, $currentEnd) {

            $query->whereBetween('created_at', [$currentStart, $currentEnd])
                ->where('status', OrderStatus::DELIVERED->value);

        })
            ->with('product')
            ->selectRaw('
            product_id,
            product_variant_id,
            product_name,
            SUM(quantity) as unitsSold,
            SUM(unit_price * quantity) as totalSales
        ')
            ->groupBy(
                'product_id',
                'product_variant_id',
                'product_name'
            )
            ->orderByDesc('unitsSold')
            ->take(5)
            ->get();

        $topProducts = $topProductsRaw->map(function ($item) {

            return [
                'name' => $item->product_name,
                'unitsSold' => $item->unitsSold,
                'sales' => $item->totalSales,
                'stock' => $item->product
                    ? $item->product->totalStock
                    : 0,
            ];
        });

        return view('admin.reports.overview', compact(
            'calculateMetrics',
            'chartData',
            'quickFacts',
            'filter',
            'topProducts',
            'ordersReturnsChart'
        ));
    }

    public function financial(Request $request)
    {
        $filter = $request->get('range', 'daily');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($filter !== 'custom') {

            $dates = $this->getDateRange($filter, $dateFrom, $dateTo);

            $currentStart = $dates['currentStart'];
            $currentEnd = $dates['currentEnd'];

            $lastStart = $dates['lastStart'];
            $lastEnd = $dates['lastEnd'];

        } elseif ($filter === 'custom') {

            $currentStart = Carbon::parse($dateFrom)->startOfDay();
            $currentEnd = Carbon::parse($dateTo)->endOfDay();

            $days = Carbon::parse($dateFrom)
                ->diffInDays($dateTo) + 1;

            $lastEnd = Carbon::parse($dateFrom)->subDay()->endOfDay();

            $lastStart = $lastEnd->copy()
                ->subDays($days - 1)
                ->startOfDay();
        }

        $currentMetrics = $this->calculateMetrics($currentStart, $currentEnd);

        $lastMetrics = $this->calculateMetrics($lastStart, $lastEnd);

        $calculateChange = function ($current, $last) {
            return $last > 0
                ? (($current - $last) / $last) * 100
                : 100;
        };

        $changes = [
            'revenue' => $calculateChange(
                $currentMetrics['totalRevenue'],
                $lastMetrics['totalRevenue']
            ),

            'grossProfit' => $calculateChange(
                $currentMetrics['grossProfit'],
                $lastMetrics['grossProfit']
            ),

            'netProfit' => $calculateChange(
                $currentMetrics['netProfit'],
                $lastMetrics['netProfit']
            ),

            'profitMargin' => $currentMetrics['profitMargin']
                - $lastMetrics['profitMargin'],

            'expense' => $calculateChange(
                $currentMetrics['totalExpense'],
                $lastMetrics['totalExpense']
            ),
        ];

        $inventoryValue = Product::with(['variants'])
            ->get()
            ->sum(function ($product) {
                if ($product->variants->isNotEmpty()) {
                    return $product->variants->sum(function ($variant) use ($product) {
                        return $product->cost_price * $variant->currentStock;
                    });
                }
                return $product->cost_price * $product->currentStock;
            });

        $lowTurnoverDays = 90;

        $soldProductIds = OrderItem::whereHas('order', function ($query) use ($lowTurnoverDays) {
            $query->where('created_at', '>=', now()->subDays($lowTurnoverDays));
        })
            ->pluck('product_id')
            ->unique();

        $lowTurnoverCount = Product::whereNotIn('id', $soldProductIds)
            ->count();

        $inventoryByCategory = Product::with(['category', 'variants'])
            ->get()
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {

                $stockValue = $items->sum(function ($product) {
                    if ($product->variants->isNotEmpty()) {
                        return $product->variants->sum(function ($variant) use ($product) {
                            return $product->cost_price * $variant->currentStock;
                        });
                    }
                    return $product->cost_price * $product->currentStock;
                });

                $totalSkuCount = $items->sum(function ($product) {
                    return $product->variants->isNotEmpty()
                        ? $product->variants->count()
                        : 1;
                });

                return [
                    'categoryId' => $categoryId,
                    'skuCount' => $totalSkuCount,
                    'stockValue' => $stockValue,
                    'category' => $items->first()->category,
                ];
            })
            ->values();

        $totalStockValue = $inventoryByCategory->sum('stockValue');

        $trendData = $this->getTrendData($filter, $dateFrom, $dateTo);

        $expenseTrend = $this->getExpenseTrend($filter, $dateFrom, $dateTo);

        $incomeSources = [
            'Product Sales' => Order::where('is_pos', 0)->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total'),

            'POS Sales' => Order::where('is_pos', 1)
                ->whereBetween('created_at', [$currentStart, $currentEnd])
                ->sum('total'),
        ];

        $totalIncome = array_sum($incomeSources);

        $incomeData = collect($incomeSources)->map(function ($amount, $source) use ($totalIncome) {
            return [
                'source' => $source,
                'amount' => $amount,
                'percentage' => $totalIncome > 0
                    ? ($amount / $totalIncome) * 100
                    : 0,
                'status' => $source === 'Product Sales'
                    ? 'Primary Source'
                    : 'New Stream',
                'badgeClass' => $source === 'Product Sales'
                    ? 'bg-primary'
                    : 'bg-info',
            ];
        });

        $totalExpense = $currentMetrics['totalExpense'];

        $expenseCategories = Expense::whereBetween('created_at', [$currentStart, $currentEnd])
            ->select('category_id', DB::raw('SUM(amount) as totalAmount'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $highestExpense = $expenseCategories
            ->sortByDesc('totalAmount')
            ->first();

        $lastTotalExpense = $lastMetrics['totalExpense'];

        $expenseGrowth = $lastTotalExpense > 0
            ? (($totalExpense - $lastTotalExpense) / $lastTotalExpense) * 100
            : 100;

        return view('admin.reports.financial', compact(
            'currentMetrics',
            'lastMetrics',
            'changes',
            'inventoryValue',
            'trendData',
            'incomeData',
            'filter',
            'totalExpense',
            'expenseCategories',
            'highestExpense',
            'expenseGrowth',
            'expenseTrend',
            'lastStart',
            'lastEnd',
            'lowTurnoverDays',
            'lowTurnoverCount',
            'inventoryByCategory',
            'totalStockValue'
        ));
    }

    public function sales(Request $request)
    {
        $filter = $request->get('range', 'daily');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($filter && $filter !== 'custom') {

            $dates = $this->getDateRange($filter);

            $currentStart = $dates['currentStart'];
            $currentEnd = $dates['currentEnd'];

            $lastStart = $dates['lastStart'];
            $lastEnd = $dates['lastEnd'];

        } elseif ($filter === 'custom') {

            $currentStart = Carbon::parse($dateFrom)->startOfDay();
            $currentEnd = Carbon::parse($dateTo)->endOfDay();

            $days = Carbon::parse($dateFrom)->diffInDays($dateTo) + 1;

            $lastEnd = Carbon::parse($dateFrom)->subDay()->endOfDay();
            $lastStart = $lastEnd->copy()->subDays($days - 1)->startOfDay();

        } else {

            $currentStart = Order::min('created_at') ?? now();
            $currentEnd = now();

            $lastStart = null;
            $lastEnd = null;
        }

        $orders = Order::whereBetween('created_at', [$currentStart, $currentEnd])->get();

        $prevOrders = ($lastStart && $lastEnd)
            ? Order::whereBetween('created_at', [$lastStart, $lastEnd])->get()
            : collect();

        $refundItems = SaleReturnItem::whereHas('saleReturn', function ($q) use ($currentStart, $currentEnd) {
            $q->whereBetween('created_at', [$currentStart, $currentEnd]);
        })->get();

        $prevRefundItems = ($lastStart && $lastEnd)
            ? SaleReturnItem::whereHas('saleReturn', function ($q) use ($lastStart, $lastEnd) {
                $q->whereBetween('created_at', [$lastStart, $lastEnd]);
            })->get()
            : collect();

        $totalRefundAmount = $refundItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $prevRefundAmount = $prevRefundItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $totalRefundItems = $refundItems->count();
        $prevRefundItemsCount = $prevRefundItems->count();

        $totalRevenue = $orders->sum('total');
        $prevRevenue = $prevOrders->sum('total');

        $totalOrder = $orders->count();
        $prevOrder = $prevOrders->count();

        $avgOrder = $orders->avg('total') ?? 0;
        $prevAvgOrder = $prevOrders->avg('total') ?? 0;

        $calcGrowth = fn($current, $previous) =>
            $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;

        $revenueGrowth = $calcGrowth($totalRevenue, $prevRevenue);
        $orderGrowth = $calcGrowth($totalOrder, $prevOrder);
        $avgOrderGrowth = $calcGrowth($avgOrder, $prevAvgOrder);

        $bestSelling = OrderItem::whereHas('order', function ($q) use ($currentStart, $currentEnd) {
            $q->whereBetween('created_at', [$currentStart, $currentEnd]);
        })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product:id,name')
            ->first();

        $labels = [];
        $revenues = [];

        foreach (CarbonPeriod::create($currentStart, $currentEnd) as $date) {
            $labels[] = $date->format('d M');
            $revenues[] = Order::whereDate('created_at', $date)->sum('total');
        }

        $orderItems = OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->whereBetween('created_at', [$currentStart, $currentEnd])
        )
            ->with('product.category')
            ->get();

        $prevOrderItems = ($lastStart && $lastEnd)
            ? OrderItem::whereHas(
                'order',
                fn($q) =>
                $q->whereBetween('created_at', [$lastStart, $lastEnd])
            )
                ->with('product.category')
                ->get()
            : collect();

        $categoryData = $orderItems->groupBy(
            fn($i) =>
            $i->product->category->name ?? 'Uncategorized'
        )
            ->map(function ($items, $category) use ($prevOrderItems) {

                $sales = $items->sum(fn($i) => $i->unit_price * $i->quantity);
                $orders = $items->groupBy('order_id')->count();

                $prevSales = $prevOrderItems
                    ->filter(fn($i) => ($i->product->category->name ?? 'Uncategorized') === $category)
                    ->sum(fn($i) => $i->unit_price * $i->quantity);

                return [
                    'category' => $category,
                    'sales' => $sales,
                    'orders' => $orders,
                ];
            })
            ->values();

        $webOrders = $orders->where('is_pos', 0);
        $posOrders = $orders->where('is_pos', 1);

        $channelData = [
            [
                'channel' => 'Web / E-commerce',
                'revenue' => $webOrders->sum('total'),
                'orders' => $webOrders->count(),
            ],
            [
                'channel' => 'POS (Retail)',
                'revenue' => $posOrders->sum('total'),
                'orders' => $posOrders->count(),
            ],
        ];

        $totalChannelRevenue = $orders->sum('total');
        $maxRevenue = max(array_column($channelData, 'revenue'));

        foreach ($channelData as &$channel) {
            $channel['contribution'] = $totalChannelRevenue > 0
                ? round(($channel['revenue'] / $totalChannelRevenue) * 100, 2)
                : 0;

            $channel['isTop'] = $channel['revenue'] === $maxRevenue;
        }
        unset($channel);

        $productStats = OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->whereBetween('created_at', [$currentStart, $currentEnd])
        )
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {

                $product = $group->first()->product;

                $sales = $group->sum(fn($i) => $i->unit_price * $i->quantity);
                $cost = $group->sum(fn($i) => $i->cost_price * $i->quantity);

                $units = $group->sum('quantity');

                $margin = $sales > 0
                    ? (($sales - $cost) / $sales) * 100
                    : 0;

                return [
                    'product_name' => $product->name ?? 'Unknown',
                    'price' => $group->avg('unit_price'),
                    'units_sold' => $units,
                    'total_sales' => $sales,
                    'profit_margin' => round($margin, 2),
                    'relative_sales' => 0,
                ];
            })
            ->sortByDesc('total_sales')
            ->values();

        $maxSales = $productStats->max('total_sales');

        $productStats = $productStats->map(function ($p) use ($maxSales) {
            $p['relative_sales'] = $maxSales > 0
                ? round(($p['total_sales'] / $maxSales) * 100)
                : 0;

            return $p;
        });

        $regionData = Order::with('district')->where('is_pos',0)->whereNotNull('shipping_district')
            ->get();

        $ordersByDistrict = $regionData
            ->groupBy('shipping_district')
            ->map(function ($group, $districtId) {

                $district = District::find($districtId);

                return [
                    'district' => $district?->name,
                    'orders_count' => $group->count(),
                ];
            })
            ->values();

        $districtLabels = $ordersByDistrict->pluck('district')->toArray();
        $districtOrders = $ordersByDistrict->pluck('orders_count')->toArray();

        return view('admin.reports.sale', compact(
            'totalRevenue',
            'totalOrder',
            'avgOrder',
            'bestSelling',
            'revenueGrowth',
            'orderGrowth',
            'avgOrderGrowth',
            'labels',
            'revenues',
            'categoryData',
            'channelData',
            'productStats',
            'filter',
            'refundItems',
            'totalRefundAmount',
            'totalRefundItems',
            'districtLabels',
            'districtOrders'
        ));
    }

    public function customers(Request $request)
    {
        $filter = $request->get('range');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($filter && $filter !== 'custom') {

            $dates = $this->getDateRange($filter);

            $currentStart = $dates['currentStart'];
            $currentEnd = $dates['currentEnd'];

            $lastStart = $dates['lastStart'];
            $lastEnd = $dates['lastEnd'];

        } elseif ($filter === 'custom') {

            $currentStart = Carbon::parse($dateFrom)->startOfDay();
            $currentEnd = Carbon::parse($dateTo)->endOfDay();

            $days = Carbon::parse($dateFrom)
                ->diffInDays($dateTo) + 1;

            $lastEnd = Carbon::parse($dateFrom)
                ->subDay()
                ->endOfDay();

            $lastStart = $lastEnd->copy()
                ->subDays($days - 1)
                ->startOfDay();

        } else {

            $currentStart = Order::min('created_at') ?? now();
            $currentEnd = now();

            $lastStart = null;
            $lastEnd = null;
        }

        $allTimeTotalCustomers = Order::get(['customer_id'])
            ->unique(fn($item) => $item->user_id . '-' . $item->customer_id)
            ->count();

        $newCustomersCurrent = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->get(['user_id', 'customer_id'])
            ->unique(fn($item) => $item->user_id . '-' . $item->customer_id)
            ->count();

        $newCustomersLast = ($lastStart && $lastEnd)
            ? Order::whereBetween('created_at', [$lastStart, $lastEnd])
                ->get(['user_id', 'customer_id'])
                ->unique(fn($item) => $item->user_id . '-' . $item->customer_id)
                ->count()
            : 0;

        $returningCustomersCurrent = max($allTimeTotalCustomers - $newCustomersCurrent, 0);
        $returningPercentage = $allTimeTotalCustomers > 0
            ? round(($returningCustomersCurrent / $allTimeTotalCustomers) * 100, 1)
            : 0;

        $avgClvCurrent = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->avg('total') ?? 0;

        $avgClvLast = ($lastStart && $lastEnd)
            ? Order::whereBetween('created_at', [$lastStart, $lastEnd])
                ->avg('total') ?? 0
            : 0;

        $totalOrdersCurrent = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();

        $totalOrdersLast = ($lastStart && $lastEnd)
            ? Order::whereBetween('created_at', [$lastStart, $lastEnd])
                ->count()
            : 0;

        $avgOrdersPerCustomerCurrent = $newCustomersCurrent > 0
            ? round($totalOrdersCurrent / $newCustomersCurrent, 2)
            : 0;

        $avgOrdersPerCustomerLast = $newCustomersLast > 0
            ? round($totalOrdersLast / $newCustomersLast, 2)
            : 0;

        $newCustomersChange = $newCustomersLast > 0
            ? round((($newCustomersCurrent - $newCustomersLast) / $newCustomersLast) * 100, 1)
            : 0;

        $avgClvChange = $avgClvLast > 0
            ? round((($avgClvCurrent - $avgClvLast) / $avgClvLast) * 100, 1)
            : 0;

        $avgOrdersPerCustomerChange = $avgOrdersPerCustomerLast > 0
            ? round((($avgOrdersPerCustomerCurrent - $avgOrdersPerCustomerLast) / $avgOrdersPerCustomerLast) * 100, 1)
            : 0;

        $chartData = [
            'total' => ['labels' => [], 'data' => []],
            'new_vs_returning' => ['labels' => [], 'new' => [], 'returning' => []],
        ];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthlyTotal = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->get(['user_id', 'customer_id'])
                ->unique(fn($item) => $item->user_id . '-' . $item->customer_id)
                ->count();

            $previousTotal = Order::where('created_at', '<', $monthStart)
                ->get(['user_id', 'customer_id'])
                ->unique(fn($item) => $item->user_id . '-' . $item->customer_id)
                ->count();

            $newCustomers = max($monthlyTotal - $previousTotal, 0);
            $returningCustomers = max($monthlyTotal - $newCustomers, 0);

            $label = $month->format('M Y');
            $chartData['total']['labels'][] = $label;
            $chartData['total']['data'][] = $monthlyTotal;
            $chartData['new_vs_returning']['labels'][] = $label;
            $chartData['new_vs_returning']['new'][] = $newCustomers;
            $chartData['new_vs_returning']['returning'][] = $returningCustomers;
        }

        $topCustomers = Order::whereBetween('created_at', [$currentStart, $currentEnd])
            ->with(['user:id,name', 'customer:id,name'])
            ->selectRaw("
                user_id,
                customer_id,
                COUNT(id) as totalOrders,
                SUM(COALESCE(total,0)) as total_spent
            ")
            ->groupBy('user_id', 'customer_id')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get()
            ->map(fn($row) => [
                'name' => $row->user->name ?? $row->customer->name ?? 'Guest Customer',
                'orders' => $row->totalOrders,
                'spent' => $row->total_spent,
            ]);

        return view('admin.reports.customers', compact(
            'filter',
            'allTimeTotalCustomers',
            'newCustomersCurrent',
            'newCustomersChange',
            'returningPercentage',
            'avgClvCurrent',
            'avgClvChange',
            'avgOrdersPerCustomerCurrent',
            'avgOrdersPerCustomerChange',
            'chartData',
            'topCustomers'
        ));
    }

    protected function calculateMetrics($start, $end)
    {
        if (!$start || !$end) {
            return [
                'totalRevenue' => 0,
                'totalProductCost' => 0,
                'grossProfit' => 0,
                'totalExpense' => 0,
                'netProfit' => 0,
                'profitMargin' => 0,
            ];
        }

        $totalRevenue = Order::whereBetween('created_at', [$start, $end])
            ->sum('total');

        $orderItems = OrderItem::whereHas('order', function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        })->get();

        $totalProductCost = $orderItems->sum(function ($item) {
            return ($item->product->cost_price ?? 0) * $item->quantity;
        });

        $grossProfit = $totalRevenue - $totalProductCost;

        $totalExpense = Expense::whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $netProfit = $grossProfit - $totalExpense;

        $profitMargin = $totalRevenue > 0
            ? ($netProfit / $totalRevenue) * 100
            : 0;

        return compact(
            'totalRevenue',
            'totalProductCost',
            'grossProfit',
            'totalExpense',
            'netProfit',
            'profitMargin'
        );
    }

    protected function getDateRange($filter, $dateFrom = null, $dateTo = null)
    {
        switch ($filter) {
            case 'daily':
                $currentStart = now()->startOfDay();
                $currentEnd = now()->endOfDay();
                $lastStart = now()->subDay()->startOfDay();
                $lastEnd = now()->subDay()->endOfDay();
                break;

            case 'weekly':
                $currentStart = now()->startOfWeek();
                $currentEnd = now()->endOfWeek();
                $lastStart = now()->subWeek()->startOfWeek();
                $lastEnd = now()->subWeek()->endOfWeek();
                break;

            case 'yearly':
                $currentStart = now()->startOfYear();
                $currentEnd = now()->endOfYear();
                $lastStart = now()->subYear()->startOfYear();
                $lastEnd = now()->subYear()->endOfYear();
                break;

            case 'custom':
                $currentStart = Carbon::parse($dateFrom)->startOfDay();
                $currentEnd = Carbon::parse($dateTo)->endOfDay();
                $days = Carbon::parse($dateFrom)->diffInDays($dateTo) + 1;
                $lastEnd = Carbon::parse($dateFrom)->subDay()->endOfDay();
                $lastStart = $lastEnd->copy()->subDays($days - 1)->startOfDay();
                break;

            case 'monthly':
            default:
                $currentStart = now()->startOfMonth();
                $currentEnd = now()->endOfMonth();
                $lastStart = now()->subMonth()->startOfMonth();
                $lastEnd = now()->subMonth()->endOfMonth();
        }

        return [
            'currentStart' => $currentStart,
            'currentEnd' => $currentEnd,
            'lastStart' => $lastStart,
            'lastEnd' => $lastEnd,
        ];
    }

    protected function periodUnit($filter, $dateFrom = null, $dateTo = null)
    {
        return match ($filter) {
            'daily' => 'day',
            'weekly' => 'week',
            'yearly' => 'year',
            'monthly' => 'month',
            'custom' => 'day',
            default => 'month',
        };
    }

    protected function getTrendData($filter, $dateFrom = null, $dateTo = null)
    {
        $overallStart = now();
        $overallEnd = now();

        switch ($filter) {
            case 'daily':
                $overallStart = now()->subDays(29)->startOfDay();
                $overallEnd = now()->endOfDay();
                break;

            case 'weekly':
                $overallStart = now()->subWeeks(11)->startOfWeek();
                $overallEnd = now()->endOfWeek();
                break;

            case 'yearly':
                $overallStart = now()->subYears(4)->startOfYear();
                $overallEnd = now()->endOfYear();
                break;

            case 'custom':
                if (!$dateFrom || !$dateTo) {
                    return collect();
                }

                $overallStart = Carbon::parse($dateFrom)->startOfDay();
                $overallEnd = Carbon::parse($dateTo)->endOfDay();
                break;

            case 'monthly':
            default:
                $overallStart = now()->subMonths(11)->startOfMonth();
                $overallEnd = now()->endOfMonth();
                break;
        }

        $allOrders = Order::whereBetween('created_at', [$overallStart, $overallEnd])
            ->get();

        $allOrderItems = OrderItem::whereHas('order', function ($query) use ($overallStart, $overallEnd) {
            $query->whereBetween('created_at', [$overallStart, $overallEnd]);
        })
            ->with([
                'order:id,created_at',
                'product:id,cost_price'
            ])
            ->get();

        $allExpenses = Expense::whereBetween('created_at', [$overallStart, $overallEnd])
            ->get();

        $calculateMetrics = function ($start, $end) use ($allOrders, $allOrderItems, $allExpenses) {

            $orders = $allOrders->filter(function ($order) use ($start, $end) {
                return $order->created_at >= $start
                    && $order->created_at <= $end;
            });

            $totalRevenue = $orders->sum('total');

            $items = $allOrderItems->filter(function ($item) use ($start, $end) {
                return $item->order
                    && $item->order->created_at >= $start
                    && $item->order->created_at <= $end;
            });

            $totalProductCost = $items->sum(function ($item) {
                return ($item->product->cost_price ?? 0) * $item->quantity;
            });

            $totalSellingPrice = $items->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });

            $grossProfit = $totalSellingPrice - $totalProductCost;

            $totalExpense = $allExpenses
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $netProfit = $grossProfit - $totalExpense;

            return [
                'netProfit' => round($netProfit, 2),
                'grossProfit' => round($grossProfit, 2),
                'totalRevenue' => round($totalRevenue, 2),
            ];
        };

        $trendData = collect();

        switch ($filter) {

            case 'daily':

                for ($i = 29; $i >= 0; $i--) {

                    $start = now()
                        ->subDays($i)
                        ->startOfDay();

                    $metrics = $calculateMetrics(
                        $start,
                        $start->copy()->endOfDay()
                    );

                    $trendData->push([
                        'label' => $start->format('d M'),
                        'netProfit' => $metrics['netProfit'],
                        'grossProfit' => $metrics['grossProfit'],
                        'totalRevenue' => $metrics['totalRevenue'],
                    ]);
                }

                break;

            case 'weekly':

                for ($i = 11; $i >= 0; $i--) {

                    $start = now()
                        ->subWeeks($i)
                        ->startOfWeek();

                    $metrics = $calculateMetrics(
                        $start,
                        $start->copy()->endOfWeek()
                    );

                    $trendData->push([
                        'label' => 'Week ' . $start->weekOfYear,
                        'netProfit' => $metrics['netProfit'],
                        'grossProfit' => $metrics['grossProfit'],
                        'totalRevenue' => $metrics['totalRevenue'],
                    ]);
                }

                break;

            case 'yearly':

                for ($i = 4; $i >= 0; $i--) {

                    $start = now()
                        ->subYears($i)
                        ->startOfYear();

                    $metrics = $calculateMetrics(
                        $start,
                        $start->copy()->endOfYear()
                    );

                    $trendData->push([
                        'label' => $start->format('Y'),
                        'netProfit' => $metrics['netProfit'],
                        'grossProfit' => $metrics['grossProfit'],
                        'totalRevenue' => $metrics['totalRevenue'],
                    ]);
                }

                break;

            case 'custom':

                $days = Carbon::parse($dateFrom)
                    ->diffInDays(Carbon::parse($dateTo));

                for ($i = 0; $i <= $days; $i++) {

                    $start = Carbon::parse($dateFrom)
                        ->addDays($i)
                        ->startOfDay();

                    $metrics = $calculateMetrics(
                        $start,
                        $start->copy()->endOfDay()
                    );

                    $trendData->push([
                        'label' => $start->format('d M'),
                        'netProfit' => $metrics['netProfit'],
                        'grossProfit' => $metrics['grossProfit'],
                        'totalRevenue' => $metrics['totalRevenue'],
                    ]);
                }

                break;

            case 'monthly':
            default:

                for ($i = 11; $i >= 0; $i--) {

                    $start = now()
                        ->subMonths($i)
                        ->startOfMonth();

                    $metrics = $calculateMetrics(
                        $start,
                        $start->copy()->endOfMonth()
                    );

                    $trendData->push([
                        'label' => $start->format('M Y'),
                        'netProfit' => $metrics['netProfit'],
                        'grossProfit' => $metrics['grossProfit'],
                        'totalRevenue' => $metrics['totalRevenue'],
                    ]);
                }

                break;
        }

        return $trendData;
    }


    protected function getExpenseTrend($filter, $dateFrom = null, $dateTo = null)
    {
        $expenseTrend = collect();

        switch ($filter) {
            case 'daily':
                $days = 11; // last 12 days
                for ($i = $days; $i >= 0; $i--) {
                    $start = now()->subDays($i)->startOfDay();
                    $end = now()->subDays($i)->endOfDay();

                    $amount = Expense::whereBetween('created_at', [$start, $end])
                        ->sum('amount');

                    $expenseTrend->push([
                        'label' => $start->format('d M'),
                        'amount' => $amount,
                    ]);
                }
                break;

            case 'weekly':
                for ($i = 11; $i >= 0; $i--) { // last 12 weeks
                    $start = now()->subWeeks($i)->startOfWeek();
                    $end = now()->subWeeks($i)->endOfWeek();

                    $amount = Expense::whereBetween('created_at', [$start, $end])
                        ->sum('amount');

                    $expenseTrend->push([
                        'label' => $start->format('d M'),
                        'amount' => $amount,
                    ]);
                }
                break;

            case 'yearly':
                for ($i = 4; $i >= 0; $i--) { // last 5 years
                    $start = now()->subYears($i)->startOfYear();
                    $end = now()->subYears($i)->endOfYear();

                    $amount = Expense::whereBetween('created_at', [$start, $end])
                        ->sum('amount');

                    $expenseTrend->push([
                        'label' => $start->format('Y'),
                        'amount' => $amount,
                    ]);
                }
                break;

            case 'custom':
                $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->subMonth()->startOfMonth();
                $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfMonth();

                $period = CarbonPeriod::create($from, $to);

                foreach ($period as $date) {
                    $start = $date->copy()->startOfDay();
                    $end = $date->copy()->endOfDay();

                    $amount = Expense::whereBetween('created_at', [$start, $end])
                        ->sum('amount');

                    $expenseTrend->push([
                        'label' => $start->format('d M Y'),
                        'amount' => $amount,
                    ]);
                }
                break;

            case 'monthly':
            default:
                for ($i = 11; $i >= 0; $i--) {
                    $start = now()->subMonths($i)->startOfMonth();
                    $end = now()->subMonths($i)->endOfMonth();

                    $amount = Expense::whereBetween('created_at', [$start, $end])
                        ->sum('amount');

                    $expenseTrend->push([
                        'label' => $start->format('M Y'),
                        'amount' => $amount,
                    ]);
                }
                break;
        }

        return $expenseTrend;
    }

    public function cashRegisters()
    {
        $cashRegisters = CashRegister::latest('id')->paginate(25);

        return view('admin.reports.cash_register', compact('cashRegisters'));
    }
}
