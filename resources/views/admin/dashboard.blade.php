@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Page Header + Filter --}}
<div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-900">Dashboard</h1>
        <p class="text-slate-500">Welcome back! Here's what's happening with your store.</p>
    </div>
    <div class="flex items-center gap-2 p-1 bg-white border border-slate-100 rounded-xl shadow-sm">
        @foreach(['today' => 'Today', 'this_week' => 'This Week', 'this_month' => 'This Month'] as $key => $label)
            <a href="{{ route('admin.dashboard', ['filter' => $key]) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition {{ ($filter ?? 'today') === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Quick Stats Grid --}}
<div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">
    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold rounded-full bg-white/20 px-2 py-0.5">{{ $widgets['totalRevenuePercentage'] >= 0 ? '+' : '' }}{{ $widgets['totalRevenuePercentage'] }}%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Total Revenue</h3>
        <p class="text-2xl font-bold tabular-nums">{{ money($widgets['totalRevenue']) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold rounded-full bg-white/20 px-2 py-0.5">{{ $widgets['totalOrdersPercentage'] >= 0 ? '+' : '' }}{{ $widgets['totalOrdersPercentage'] }}%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Total Sales</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['totalOrders'], 0) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold rounded-full bg-white/20 px-2 py-0.5">{{ $widgets['totalCustomersPercentage'] >= 0 ? '+' : '' }}{{ $widgets['totalCustomersPercentage'] }}%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Customers</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['totalCustomers'], 0) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold rounded-full bg-white/20 px-2 py-0.5">Pending</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Pending Sales</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['pendingOrders'], 0) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Total Products</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['totalProducts'], 0) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="tag" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Categories</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['totalCategories'], 0) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="chart-line" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Avg Order Value</h3>
        <p class="text-2xl font-bold tabular-nums">{{ money($widgets['avgOrderValue']) }}</p>
    </div>

    <div class="relative p-5 overflow-hidden text-white shadow-lg transition transform rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 hover:-translate-y-0.5 hover:shadow-xl">
        <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/10"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            @if($widgets['outOfStock'] > 0)
            <span class="text-xs font-semibold rounded-full bg-white/20 px-2 py-0.5">Alert</span>
            @endif
        </div>
        <h3 class="mb-1 text-xs font-medium text-white/80">Out of Stock</h3>
        <p class="text-2xl font-bold tabular-nums">{{ number_format($widgets['outOfStock'], 0) }}</p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid gap-6 mb-8 lg:grid-cols-3">
    <div class="p-6 bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="mb-1 text-lg font-bold text-slate-900">Revenue Overview</h2>
                <p class="text-sm text-slate-500">Revenue vs refunds for the selected period</p>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Payment Breakdown</h2>
        <p class="mb-6 text-sm text-slate-500">Sales by payment method</p>
        <div class="chart-container" style="height: 220px;">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>
</div>

{{-- Second Charts Row --}}
<div class="grid gap-6 mb-8 lg:grid-cols-2">
    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Order Status</h2>
        <p class="mb-6 text-sm text-slate-500">Distribution by status</p>
        <div class="chart-container" style="height: 220px;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">New vs Returning</h2>
        <p class="mb-6 text-sm text-slate-500">Customer composition</p>
        <div class="chart-container" style="height: 220px;">
            <canvas id="customerChart"></canvas>
        </div>
    </div>
</div>

{{-- Category Revenue + Top Customers --}}
<div class="grid gap-6 mb-8 lg:grid-cols-3">
    <div class="p-6 bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Revenue by Category</h2>
        <p class="mb-6 text-sm text-slate-500">Top categories by revenue</p>
        <div class="space-y-4">
            @forelse($categoryRevenue as $cat)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-slate-900">{{ $cat['name'] }}</span>
                    <span class="text-sm font-semibold text-slate-700">{{ money($cat['revenue']) }} <span class="text-xs font-normal text-slate-400">{{ $cat['percentage'] }}%</span></span>
                </div>
                <div class="w-full h-2.5 rounded-full bg-slate-100">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500" style="width: {{ $cat['percentage'] }}%;"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-500">No category revenue data available.</p>
            @endforelse
        </div>
    </div>

    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Top Customers</h2>
        <p class="mb-6 text-sm text-slate-500">By total spend</p>
        <div class="space-y-4">
            @forelse($topCustomers as $customer)
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 text-xs font-bold text-white rounded-full bg-linear-to-br from-emerald-500 to-teal-500">
                    {{ substr($customer['name'], 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-slate-900">{{ $customer['name'] }}</p>
                    <p class="text-xs text-slate-500">{{ $customer['orders'] }} orders</p>
                </div>
                <span class="text-sm font-bold text-slate-900">{{ money($customer['spend']) }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500">No customer data available.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Top Products + Discount Impact --}}
<div class="grid gap-6 mb-8 lg:grid-cols-3">
    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Top Products</h2>
        <p class="mb-6 text-sm text-slate-500">Best sellers this period</p>
        <div class="space-y-4">
            @forelse($products['topProducts'] as $product)
            <div class="flex items-center gap-3">
                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="object-cover w-12 h-12 rounded-lg bg-slate-100" onerror="this.src='https://placehold.co/48x48/e2e8f0/94a3b8?text=P'">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-slate-900">{{ $product['name'] }}</p>
                    <p class="text-xs text-slate-500">{{ $product['sales'] }} sold</p>
                </div>
                <span class="text-sm font-bold text-slate-900">{{ money($product['revenue']) }}</span>
            </div>
            @empty
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-slate-100">
                    <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-slate-900">No products available</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Discount Impact</h2>
        <p class="mb-6 text-sm text-slate-500">Effect of discounts on sales</p>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-slate-50">
                <p class="text-xs text-slate-500">Gross Revenue</p>
                <p class="text-lg font-bold text-slate-900">{{ money($discountImpact['grossRevenue']) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <p class="text-xs text-slate-500">Discount Given</p>
                <p class="text-lg font-bold text-rose-600">{{ money($discountImpact['discountGiven']) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <p class="text-xs text-slate-500">Discount Rate</p>
                <p class="text-lg font-bold text-slate-900">{{ $discountImpact['percentage'] }}%</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
                <p class="text-xs text-slate-500">Discounted Sales</p>
                <p class="text-lg font-bold text-slate-900">{{ number_format($discountImpact['discountCount'] ?? 0, 0) }}</p>
            </div>
        </div>
    </div>

    <div class="p-6 text-white shadow-lg bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl">
        <h2 class="mb-1 text-lg font-bold">Inventory Value</h2>
        <p class="mb-6 text-sm text-white/70">Stock worth on hand</p>
        <p class="mb-4 text-3xl font-bold">{{ money($stockReport['stockValue']) }}</p>
        <div class="flex gap-4 text-sm">
            <div>
                <p class="text-white/60">Low Stock</p>
                <p class="text-xl font-bold">{{ number_format($stockReport['lowStock'], 0) }}</p>
            </div>
            <div>
                <p class="text-white/60">Out of Stock</p>
                <p class="text-xl font-bold">{{ number_format($stockReport['outOfStock'], 0) }}</p>
            </div>
        </div>
        <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="inline-flex items-center gap-2 px-4 py-2 mt-6 text-sm font-semibold transition bg-white rounded-lg text-slate-800 hover:bg-slate-100">
            <span>Manage Stock</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>

{{-- Hourly Heatmap + Low Stock --}}
<div class="grid gap-6 mb-8 lg:grid-cols-3">
    <div class="p-6 bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Sales Activity Heatmap</h2>
        <p class="mb-6 text-sm text-slate-500">Revenue by day of week and hour</p>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="py-1 pr-2 text-left text-slate-400"></th>
                        @foreach($heatmap['hours'] as $h)
                            @if($h % 3 === 0)
                            <th class="px-1 py-1 text-center text-slate-400">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</th>
                            @else
                            <th class="px-1 py-1 text-center text-slate-200"></th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($heatmap['days'] as $dIdx => $day)
                    <tr>
                        <td class="py-1 pr-2 text-right font-medium text-slate-500">{{ $day }}</td>
                        @foreach($heatmap['hours'] as $h)
                            @php
                                $val = $heatmap['data'][$dIdx][$h] ?? 0;
                                $intensity = $heatmapMax > 0 ? min(1, $val / $heatmapMax) : 0;
                                $bg = $val > 0 ? "rgba(99, 102, 241, " . (0.15 + $intensity * 0.85) . ")" : "rgb(241, 245, 249)";
                                $fg = $intensity > 0.55 ? "#fff" : "rgb(71, 85, 105)";
                            @endphp
                            <td class="p-0.5">
                                <div class="flex items-center justify-center w-6 h-6 rounded" style="background: {{ $bg }}; color: {{ $fg }};" title="{{ $day }} {{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00 — {{ money($val) }}">
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Low Stock Alert</h2>
        <p class="mb-6 text-sm text-slate-500">{{ $products['lowStockCount'] }} products running low</p>
        <div class="space-y-3">
            @forelse($products['lowStockProducts'] as $p)
            <div class="flex items-center gap-3">
                <img src="{{ $p->image }}" alt="{{ $p->name }}" class="object-cover w-10 h-10 rounded-lg bg-slate-100" onerror="this.src='https://placehold.co/40x40/e2e8f0/94a3b8?text=P'">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-slate-900">{{ $p->name }}</p>
                    <p class="text-xs text-slate-500">Left: {{ number_format($p->stock_quantity, 0) }} / Alert: {{ number_format($p->stock_alert_quantity, 0) }}</p>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Low</span>
            </div>
            @empty
            <p class="text-sm text-slate-500">All products are well stocked.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Sales + Quick Actions --}}
<div class="grid gap-6 lg:grid-cols-3">
    <div class="overflow-hidden bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="mb-1 text-lg font-bold text-slate-900">Recent Sales</h2>
                    <p class="text-sm text-slate-500">Latest sales from your store</p>
                </div>
                <a href="{{ route('admin.ecommerce-sales.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-700">View All</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b bg-slate-50 border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Invoice</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Customer</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Date</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Total</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-slate-900">{{ $order['invoice_number'] ?? '#'.$order['id'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white rounded-full bg-linear-to-br from-indigo-500 to-violet-500">
                                    {{ substr($order['customer_name'], 0, 1) }}
                                </div>
                                <span class="text-sm text-slate-900">{{ $order['customer_name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-slate-500">
                            {{ $order['sale_date'] }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap text-slate-900">
                            {{ money($order['total_amount']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $order['status'];
                                $badge = match($status) {
                                    'pending' => 'text-amber-700 bg-amber-100',
                                    'confirmed', 'processing' => 'text-indigo-700 bg-indigo-100',
                                    'shipped' => 'text-sky-700 bg-sky-100',
                                    'delivered', 'completed' => 'text-emerald-700 bg-emerald-100',
                                    'cancelled', 'returned' => 'text-rose-700 bg-rose-100',
                                    default => 'text-slate-700 bg-slate-100',
                                };
                                $label = \Illuminate\Support\Str::title(str_replace('_', ' ', $status));
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.ecommerce-sales.show', $order['id']) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-sm text-center text-slate-500">
                            No recent sales available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
            <h2 class="mb-1 text-lg font-bold text-slate-900">Quick Actions</h2>
            <p class="mb-6 text-sm text-slate-500">Common tasks and shortcuts</p>
            <div class="space-y-3">
                <a href="{{ route('admin.products.create') }}" class="flex items-center gap-3 p-3 transition hover:bg-slate-50 rounded-xl group">
                    <div class="flex items-center justify-center w-10 h-10 text-indigo-600 transition bg-indigo-100 rounded-lg group-hover:bg-indigo-600 group-hover:text-white">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Add Product</p>
                        <p class="text-xs text-slate-500">Create new product</p>
                    </div>
                </a>
                <a href="{{ route('admin.ecommerce-sales.index') }}" class="flex items-center gap-3 p-3 transition hover:bg-slate-50 rounded-xl group">
                    <div class="flex items-center justify-center w-10 h-10 transition rounded-lg bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">View Sales</p>
                        <p class="text-xs text-slate-500">Manage all sales</p>
                    </div>
                </a>
                <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 p-3 transition hover:bg-slate-50 rounded-xl group">
                    <div class="flex items-center justify-center w-10 h-10 transition rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white">
                        <i data-lucide="monitor" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Open POS</p>
                        <p class="text-xs text-slate-500">New counter sale</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartFont = { family: 'Inter, ui-sans-serif, system-ui, sans-serif' };

    // Revenue vs Refund line chart
    (function () {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chart['labels']),
                datasets: [
                    {
                        label: 'Revenue',
                        data: @json($chart['revenue']),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4, fill: true,
                        pointRadius: 4, pointHoverRadius: 6,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff', pointBorderWidth: 2,
                    },
                    {
                        label: 'Refunds',
                        data: @json($chart['refunds']),
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.08)',
                        tension: 0.4, fill: true,
                        pointRadius: 4, pointHoverRadius: 6,
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#fff', pointBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { labels: { ...chartFont, usePointStyle: true, boxWidth: 8 } },
                    tooltip: {
                        backgroundColor: 'rgb(15, 23, 42)', padding: 12,
                        titleColor: '#fff', bodyColor: '#fff', borderColor: 'rgb(51, 65, 85)', borderWidth: 1,
                        callbacks: { label: (c) => c.dataset.label + ': ৳' + Number(c.parsed.y).toLocaleString() }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { ...chartFont, callback: (v) => '৳' + (v / 1000) + 'k' }
                    },
                    x: { grid: { display: false }, ticks: { ...chartFont } }
                }
            }
        });
    })();

    // Payment breakdown doughnut
    (function () {
        const ctx = document.getElementById('paymentChart');
        if (!ctx) return;
        const data = @json($paymentBreakdown);
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.method),
                datasets: [{
                    data: data.map(d => d.total),
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                    borderWidth: 2, borderColor: '#fff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { ...chartFont, usePointStyle: true, boxWidth: 8, padding: 12 } },
                    tooltip: {
                        backgroundColor: 'rgb(15, 23, 42)', padding: 12, bodyColor: '#fff', borderColor: 'rgb(51, 65, 85)', borderWidth: 1,
                        callbacks: { label: (c) => c.label + ': ৳' + Number(c.parsed).toLocaleString() }
                    }
                }
            }
        });
    })();

    // Order status bar
    (function () {
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;
        const data = @json($orderStatusBreakdown);
        const colors = {
            draft: '#94a3b8', pending: '#f59e0b', confirmed: '#6366f1', shipped: '#0ea5e9',
            delivered: '#10b981', completed: '#059669', cancelled: '#ef4444', returned: '#f43f5e'
        };
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
                datasets: [{
                    label: 'Orders',
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => colors[d.status] || '#6366f1'),
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgb(15, 23, 42)', padding: 12, bodyColor: '#fff', borderColor: 'rgb(51, 65, 85)', borderWidth: 1,
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { ...chartFont, precision: 0 } },
                    x: { grid: { display: false }, ticks: { ...chartFont } }
                }
            }
        });
    })();

    // New vs Returning doughnut
    (function () {
        const ctx = document.getElementById('customerChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Returning'],
                datasets: [{
                    data: [{{ $customerSplit['new'] ?? 0 }}, {{ $customerSplit['returning'] ?? 0 }}],
                    backgroundColor: ['#8b5cf6', '#10b981'],
                    borderWidth: 2, borderColor: '#fff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { ...chartFont, usePointStyle: true, boxWidth: 8, padding: 12 } },
                    tooltip: {
                        backgroundColor: 'rgb(15, 23, 42)', padding: 12, bodyColor: '#fff', borderColor: 'rgb(51, 65, 85)', borderWidth: 1,
                        callbacks: { label: (c) => c.label + ': ' + Number(c.parsed).toLocaleString() + ' customers' }
                    }
                }
            }
        });
    })();
</script>
@endpush
