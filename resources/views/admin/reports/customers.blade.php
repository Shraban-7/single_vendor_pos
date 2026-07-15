@extends('admin.layouts.app')

@section('title', 'Customers Reports')
@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Customer Report</h1>
            <p class="text-xs text-slate-500">Track customer acquisition, retention, and lifetime value</p>
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
        {{-- Total Customers --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Customers</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($allTimeTotalCustomers) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $newCustomersChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $newCustomersChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($newCustomersChange), 2) }}%
                </span>
            </div>
        </div>

        {{-- New Customers --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100 shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">New Customers</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($newCustomersCurrent) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $newCustomersChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $newCustomersChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($newCustomersChange), 2) }}%
                </span>
            </div>
        </div>

        {{-- Returning Rate --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="repeat" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Returning Rate</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $returningPercentage }}%</p>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400">Of total customers</div>
        </div>

        {{-- Avg CLV --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="hand-coins" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Avg CLV</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($avgClvCurrent) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $avgClvChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $avgClvChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($avgClvChange), 2) }}%
                </span>
            </div>
        </div>

        {{-- Avg Orders --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-violet-50 text-violet-600 border border-violet-100 shrink-0">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Avg Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($avgOrdersPerCustomerCurrent, 1) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $avgOrdersPerCustomerChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $avgOrdersPerCustomerChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($avgOrdersPerCustomerChange), 2) }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 items-stretch">
        {{-- Customer Growth Trend --}}
        <div class="lg:col-span-6">
            <div class="h-full bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Customer Growth Trend</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Track customer acquisition over time</p>
                    </div>
                    <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-0.5">
                        <button type="button" onclick="showCustomerChart('total')" id="totalTab"
                            class="rounded-md bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-900 shadow-sm">
                            Total
                        </button>
                        <button type="button" onclick="showCustomerChart('returning')" id="returningTab"
                            class="rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 hover:text-slate-900">
                            New vs Returning
                        </button>
                    </div>
                </div>
                <div class="flex-1">
                    <div id="totalChartWrapper" class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                        <canvas id="totalCustomersChart" height="150"></canvas>
                    </div>
                    <div id="returningChartWrapper" class="hidden bg-slate-50 border border-slate-200 rounded-lg p-3">
                        <canvas id="newReturningChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top High-Value Customers --}}
        <div class="lg:col-span-6">
            <div class="h-full bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col">
                <div class="mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top High-Value Customers</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Customers with highest spending</p>
                </div>
                <div class="flex-1 overflow-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr class="text-slate-500">
                                <th class="px-3 py-2 text-left font-semibold">Customer</th>
                                <th class="px-3 py-2 text-right font-semibold">Sales</th>
                                <th class="px-3 py-2 text-right font-semibold">Spent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($topCustomers as $index => $cust)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2.5 min-w-[140px]">
                                        <div class="flex items-center gap-2 font-medium text-slate-800">
                                            @if ($index === 0)
                                                <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                            @elseif ($index === 1)
                                                <i data-lucide="medal" class="w-3.5 h-3.5 text-slate-500 shrink-0"></i>
                                            @elseif ($index === 2)
                                                <i data-lucide="medal" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                            @endif
                                            <span class="truncate max-w-[130px]">{{ $cust['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $cust['orders'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-semibold text-emerald-600">
                                        {{ money($cust['spent']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-8 text-center text-slate-400 text-xs">No customers found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates(value) {
        const custom = document.getElementById('customDateRange');
        custom.classList.toggle('hidden', value !== 'custom');
        custom.classList.toggle('flex', value === 'custom');
    }

    function showCustomerChart(type) {
        const totalWrapper = document.getElementById('totalChartWrapper');
        const returningWrapper = document.getElementById('returningChartWrapper');
        const totalTab = document.getElementById('totalTab');
        const returningTab = document.getElementById('returningTab');

        if (type === 'total') {
            totalWrapper.classList.remove('hidden');
            returningWrapper.classList.add('hidden');
            totalTab.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            returningTab.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        } else {
            returningWrapper.classList.remove('hidden');
            totalWrapper.classList.add('hidden');
            returningTab.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            totalTab.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        }
        setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
    }

    new Chart(document.getElementById('totalCustomersChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['total']['labels']) !!},
            datasets: [{
                label: 'Total Customers',
                data: {!! json_encode($chartData['total']['data']) !!},
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderColor: '#2563eb',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
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

    new Chart(document.getElementById('newReturningChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['new_vs_returning']['labels']) !!},
            datasets: [
                { label: 'New Customers', data: {!! json_encode($chartData['new_vs_returning']['new']) !!}, borderColor: '#14b8a6', backgroundColor: 'rgba(20, 184, 166, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#14b8a6', pointBorderColor: '#fff', pointBorderWidth: 2 },
                { label: 'Returning Customers', data: {!! json_encode($chartData['new_vs_returning']['returning']) !!}, borderColor: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#f59e0b', pointBorderColor: '#fff', pointBorderWidth: 2 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top', align: 'end', labels: { padding: 15, font: { size: 11 }, color: '#475569', usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#fff', padding: 12, cornerRadius: 8 }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { color: '#64748b', font: { size: 11 } } },
                x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b', font: { size: 11 } } }
            }
        }
    });
</script>
@endpush

@endsection
