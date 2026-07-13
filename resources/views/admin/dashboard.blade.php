@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
{{--<div class="mb-8">
    <h1 class="mb-2 text-2xl font-bold md:text-3xl text-slate-900">Dashboard</h1>
    <p class="text-slate-500">Welcome back! Here's what's happening with your store today.</p>
</div>--}}

{{-- Quick Stats Grid --}}
<div class="grid grid-cols-2 gap-4 mb-8 md:grid-cols-4">
    {{-- Total Revenue --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 text-indigo-600 bg-indigo-500/10 rounded-xl">
                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+12%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Total Revenue</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ money($widgets['totalRevenue']) }}</p>
    </div>

    {{-- Total Orders --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-violet-500/10 text-violet-600 rounded-xl">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+8%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Total Orders</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['totalOrders'], 0) }}</p>
    </div>

    {{-- Total Customers --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-emerald-500/10 text-emerald-600 rounded-xl">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+15%</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Customers</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['totalCustomers'], 0) }}</p>
    </div>

    {{-- Pending Orders --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-amber-500/10 text-amber-600 rounded-xl">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Pending</span>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Pending Orders</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['pendingOrders'], 0) }}</p>
    </div>

    {{-- Total Products --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-sky-500/10 text-sky-600 rounded-xl">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Total Products</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['totalProducts'], 0) }}</p>
    </div>

    {{-- Total Categories --}}
    <!-- <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 text-pink-600 bg-pink-500/10 rounded-xl">
                <i data-lucide="tag" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Categories</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['totalCategories'], 0) }}</p>
    </div> -->

    {{-- Average Order Value --}}
    <!-- <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 text-teal-600 bg-teal-500/10 rounded-xl">
                <i data-lucide="chart-line" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Avg Order Value</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ money($widgets['avgOrderValue']) }}</p>
    </div> -->

    {{-- Today's Orders --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-sky-500/10 text-sky-600 rounded-xl">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Today's Orders</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['todayOrders'], 0) }}</p>
    </div>

    {{-- Today's Revenue --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-emerald-500/10 text-emerald-600 rounded-xl">
                <i data-lucide="coins" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Today's Revenue</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ money($widgets['todayRevenue']) }}</p>
    </div>

    {{-- Out of Stock --}}
    <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-rose-500/10 text-rose-600 rounded-xl">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            @if($widgets['outOfStock'] > 0)
            <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">Alert</span>
            @endif
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Out of Stock</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['outOfStock'], 0) }}</p>
    </div>

    {{-- Total Reviews --}}
    <!-- <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-amber-500/10 text-amber-600 rounded-xl">
                <i data-lucide="star" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Reviews</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['totalReviews'], 0) }}</p>
    </div> -->

    {{-- Active Coupons --}}
    <!-- <div class="p-4 transition bg-white border shadow-sm rounded-2xl border-slate-100 hover:shadow-md">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-10 h-10 bg-violet-500/10 text-violet-600 rounded-xl">
                <i data-lucide="ticket-percent" class="w-5 h-5"></i>
            </div>
        </div>
        <h3 class="mb-1 text-xs font-medium text-slate-500">Active Coupons</h3>
        <p class="text-xl font-bold text-slate-900 tabular-nums">{{ number_format($widgets['activeCoupons'], 0) }}</p>
    </div> -->
</div>

{{-- Charts and Recent Activity --}}
<div class="grid gap-6 mb-8 lg:grid-cols-3">
    {{-- Revenue Chart --}}
    <div class="p-6 bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="mb-1 text-lg font-bold text-slate-900">Revenue Overview</h2>
                <p class="text-sm text-slate-500">Monthly revenue for the past 6 months</p>
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg">6M</button>
                <button class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 rounded-lg">1Y</button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="p-6 bg-white border shadow-sm rounded-2xl border-slate-100">
        <h2 class="mb-1 text-lg font-bold text-slate-900">Top Products</h2>
        <p class="mb-6 text-sm text-slate-500">Best selling products this month</p>

        <div class="space-y-4">
            @forelse($topProducts ?? [] as $product)
            <div class="flex items-center gap-3">
                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="object-cover w-12 h-12 rounded-lg bg-slate-100">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-slate-900">{{ $product['name'] }}</p>
                    <p class="text-xs text-slate-500">{{ $product['sales'] }} sold</p>
                </div>
                <span class="text-sm font-bold text-slate-900">৳{{ number_format($product['revenue'], 0) }}</span>
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

        <a href="{{ route('admin.products.index') }}" class="mt-6 flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
            <span>View All Products</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>

{{-- Recent Orders and Activity --}}
<div class="grid gap-6 lg:grid-cols-3">
    {{-- Recent Orders --}}
    <div class="overflow-hidden bg-white border shadow-sm lg:col-span-2 rounded-2xl border-slate-100">
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="mb-1 text-lg font-bold text-slate-900">Recent Orders</h2>
                    <p class="text-sm text-slate-500">Latest orders from your store</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-700">View All</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b bg-slate-50 border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Order ID</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Customer</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Date</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Total</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left uppercase text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-slate-900">#{{ $order['id'] }}</span>
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
                            {{ $order['created_at'] }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap text-slate-900">
                            ৳{{ number_format($order['total'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order['status'] === 'pending')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full text-amber-700 bg-amber-100">Pending</span>
                            @elseif($order['status'] === 'processing')
                            <span class="px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">Processing</span>
                            @elseif($order['status'] === 'completed')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full text-emerald-700 bg-emerald-100">Completed</span>
                            @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full text-rose-700 bg-rose-100">Cancelled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $order['id']) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-sm text-center text-slate-500">
                            No recent orders available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions & Stats --}}
    <div class="space-y-6">
        {{-- Quick Actions --}}
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
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 p-3 transition hover:bg-slate-50 rounded-xl group">
                    <div class="flex items-center justify-center w-10 h-10 transition rounded-lg bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">View Orders</p>
                        <p class="text-xs text-slate-500">Manage all orders</p>
                    </div>
                </a>
                <a href="{{ route('admin.coupons.create') }}" class="flex items-center gap-3 p-3 transition hover:bg-slate-50 rounded-xl group">
                    <div class="flex items-center justify-center w-10 h-10 transition rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white">
                        <i data-lucide="ticket-percent" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Create Coupon</p>
                        <p class="text-xs text-slate-500">New discount code</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="p-6 text-white shadow-lg bg-gradient-to-br from-amber-500 to-rose-500 rounded-2xl">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/20 backdrop-blur">Alert</span>
            </div>
            <h3 class="mb-2 text-lg font-bold">Low Stock Products</h3>
            <p class="mb-4 text-sm text-white/90">{{ $lowStockCount ?? 8 }} products are running low on stock</p>
            <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold transition bg-white rounded-lg text-amber-600 hover:bg-amber-50">
                <span>View Products</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                datasets: [{
                    label: 'Revenue',
                    data: [45000, 52000, 48000, 61000, 55000, 68000],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgb(15, 23, 42)',
                        padding: 12,
                        titleColor: 'rgb(255, 255, 255)',
                        bodyColor: 'rgb(255, 255, 255)',
                        borderColor: 'rgb(51, 65, 85)',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '৳' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        },
                        ticks: {
                            callback: function(value) {
                                return '৳' + (value / 1000) + 'k';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
