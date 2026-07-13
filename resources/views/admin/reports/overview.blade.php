@extends('admin.layouts.app')

@section('title', 'Report Overview')
@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Business Overview</h1>
            <p class="text-xs text-slate-500">Key metrics and performance indicators</p>
        </div>
        <div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="range" onchange="toggleCustomDates(this.value)"
                    class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="daily" {{ request('range') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('range') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('range') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ request('range') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="custom" {{ request('range') == 'custom' ? 'selected' : '' }}>Custom</option>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
        {{-- Sales --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($calculateMetrics['totalSales']) }}</p>
                </div>
            </div>
            <div class="mt-2 flex items-center text-[10px]">
                <span class="{{ $calculateMetrics['salesGrowth'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $calculateMetrics['salesGrowth'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($calculateMetrics['salesGrowth']), 2) }}%
                </span>
                <span class="text-slate-400 ml-1">vs last {{ request('range') }}</span>
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100 shrink-0">
                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Orders</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $calculateMetrics['totalOrders'] }}</p>
                </div>
            </div>
            <div class="mt-2 flex items-center text-[10px]">
                <span class="{{ $calculateMetrics['ordersGrowth'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $calculateMetrics['ordersGrowth'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($calculateMetrics['ordersGrowth']), 2) }}%
                </span>
                <span class="text-slate-400 ml-1">vs last {{ request('range') }}</span>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Net Profit</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($calculateMetrics['netProfit']) }}</p>
                </div>
            </div>
            <div class="mt-2 flex items-center text-[10px]">
                <span class="{{ $calculateMetrics['profitGrowth'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $calculateMetrics['profitGrowth'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($calculateMetrics['profitGrowth']), 2) }}%
                </span>
                <span class="text-slate-400 ml-1">vs last {{ request('range') }}</span>
            </div>
        </div>

        {{-- Returning Customers --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Ret. Customers</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($quickFacts['returningCustomersPercent'], 2) }}%</p>
                </div>
            </div>
        </div>

        {{-- AOV --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-violet-50 text-violet-600 border border-violet-100 shrink-0">
                    <i data-lucide="basket" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">AOV</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($calculateMetrics['aov']) }}</p>
                </div>
            </div>
        </div>

        {{-- Stock --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-50 text-slate-500 border border-slate-100 shrink-0">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Stock</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $calculateMetrics['totalStock'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Revenue & Order Trends --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Revenue & Order Trends</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 mb-2">{{ request('range') }} Revenue Trend</p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 h-72">
                        <canvas id="revenueTrend"></canvas>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 mb-2">Orders vs Returns</p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 h-72 relative">
                        <canvas id="ordersReturns"></canvas>
                        <div class="absolute top-3 right-3 bg-rose-50 text-rose-700 px-2 py-0.5 rounded text-[10px] font-semibold border border-rose-100">
                            {{ $ordersReturnsChart['return_rate'] }}% Return Rate
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Facts --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Quick Facts</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs text-slate-500">Total Orders</span>
                    <span class="text-xs font-bold text-blue-600">{{ $quickFacts['totalOrders'] }}</span>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs text-slate-500">Refund Rate</span>
                    <span class="text-xs font-bold text-rose-500">{{ $quickFacts['refundRate'] }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Best Sales Day</span>
                    <span class="text-xs font-bold text-emerald-600 text-right">{{ $quickFacts['bestSalesDay'] ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Products Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Product Snapshot</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3 text-right">Units Sold</th>
                        <th class="px-4 py-3 text-right">Sales</th>
                        <th class="px-4 py-3 text-right">Stock</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @foreach ($topProducts as $product)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $product['name'] }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-600">{{ $product['unitsSold'] }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold text-emerald-600">{{ money($product['sales']) }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-600">{{ $product['stock'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates(value) {
        const custom = document.getElementById('customDateRange');
        custom.style.display = (value === 'custom') ? 'flex' : 'none';
    }

    new Chart(document.getElementById('revenueTrend'), {
        type: 'line',
        data: {
            labels: @json($chartData['revenueTrend']['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($chartData['revenueTrend']['values']),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 6,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#64748b', font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 10 } }
                }
            }
        }
    });

    new Chart(document.getElementById('ordersReturns').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($ordersReturnsChart['labels']),
            datasets: [{
                label: 'Order Count',
                data: @json($ordersReturnsChart['data']),
                backgroundColor: @json($ordersReturnsChart['colors']),
                borderColor: @json($ordersReturnsChart['colors']),
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = {{ $ordersReturnsChart['total_orders'] }};
                            const value = context.raw;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${value} orders (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#e2e8f0', drawBorder: false }, ticks: { color: '#64748b', font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11, weight: '600' } } }
            }
        }
    });
</script>
@endpush

@endsection
