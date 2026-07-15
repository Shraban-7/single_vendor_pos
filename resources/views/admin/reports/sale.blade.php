@extends('admin.layouts.app')

@section('title', 'Sales Reports')
@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Sales Report</h1>
            <p class="text-xs text-slate-500">Track your sales performance over time</p>
        </div>
        <div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="range" onchange="toggleCustomDates(this.value)"
                    class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="daily" {{ request('range') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('range') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('range') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ request('range') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="custom" {{ request('range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
                <div id="customDateRange" class="{{ request('range') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-1 px-3 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg shadow-sm hover:bg-slate-900 transition">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Filter</span>
                </button>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($totalRevenue) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $revenueGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $revenueGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($revenueGrowth), 2) }}%
                </span>
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100 shrink-0">
                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($totalOrder) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $orderGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $orderGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($orderGrowth), 2) }}%
                </span>
            </div>
        </div>

        {{-- Average Order Value --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="chart-line" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Avg Sale Value</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($avgOrder) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $avgOrderGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $avgOrderGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($avgOrderGrowth), 2) }}%
                </span>
            </div>
        </div>

        {{-- Growth Rate --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Growth Rate</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $avgOrderGrowth }}%</p>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400">Period comparison</div>
        </div>

        {{-- Refund Items --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Refund Items</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($totalRefundItems) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400">Returned products count</div>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Revenue Trend</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Track your revenue performance over time</p>
            </div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
            <canvas id="revenueTrendChart" height="80"></canvas>
        </div>
    </div>

    {{-- Middle Section --}}
    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Category Performance --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Category Performance</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Sales breakdown by category</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex items-center justify-center bg-slate-50 border border-slate-200 rounded-lg p-3">
                    <canvas id="categoryPieChart"></canvas>
                </div>
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50">
                            <tr class="text-slate-600">
                                <th class="px-3 py-2 text-left font-semibold">Category</th>
                                <th class="px-3 py-2 text-right font-semibold">Sales</th>
                                <th class="px-3 py-2 text-right font-semibold">Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($categoryData ?? [] as $data)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-3 py-2 font-medium text-slate-800">{{ $data['category'] }}</td>
                                    <td class="px-3 py-2 text-right text-slate-600">{{ money($data['sales']) }}</td>
                                    <td class="px-3 py-2 text-right text-slate-600">{{ $data['orders'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Products</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Best performing products by sales volume</p>
            </div>
            <div class="overflow-hidden rounded-lg border border-slate-200">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600">
                            <th class="px-3 py-2 text-left font-semibold">Product</th>
                            <th class="px-3 py-2 text-right font-semibold">Price</th>
                            <th class="px-3 py-2 text-right font-semibold">Units Sold</th>
                            <th class="px-3 py-2 text-right font-semibold">Total Sales</th>
                            <th class="px-3 py-2 text-right font-semibold">Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($productStats ?? [] as $prod)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-3 py-2 font-semibold text-slate-800">{{ $prod['product_name'] }}</td>
                                <td class="px-3 py-2 text-right text-slate-600">{{ money($prod['price']) }}</td>
                                <td class="px-3 py-2 text-right text-slate-600">{{ number_format($prod['units_sold']) }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-emerald-600">{{ money($prod['total_sales']) }}</td>
                                <td class="px-3 py-2 text-right">
                                    <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $prod['profit_margin'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- District Pie Chart --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="mb-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Sales by District</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Distribution of sales across regions</p>
        </div>
        <div class="relative h-[280px] flex items-center justify-center bg-slate-50 border border-slate-200 rounded-lg p-3">
            <canvas id="districtPieChart"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates(value) {
        const el = document.getElementById('customDateRange');
        el.classList.toggle('hidden', value !== 'custom');
        el.classList.toggle('flex', value === 'custom');
    }

    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
            labels: @json($labels ?? []),
            datasets: [{
                label: 'Revenue',
                data: @json($revenues ?? []),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#fff', padding: 12, cornerRadius: 8, displayColors: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { color: '#64748b', font: { size: 11 } } },
                x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b', font: { size: 11 } } }
            }
        }
    });

    new Chart(document.getElementById('categoryPieChart'), {
        type: 'doughnut',
        data: {
            labels: @json($categoryData->pluck('category') ?? []),
            datasets: [{
                data: @json($categoryData->pluck('sales') ?? []),
                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, font: { size: 11 }, color: '#475569' } },
                tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#fff', padding: 12, cornerRadius: 8 }
            }
        }
    });

    new Chart(document.getElementById('districtPieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($districtLabels) !!},
            datasets: [{
                data: {!! json_encode($districtOrders) !!},
                backgroundColor: ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316'],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#475569', font: { size: 11 } } },
                tooltip: { backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#fff' }
            }
        }
    });
</script>
@endpush

@endsection
