@extends('admin.layouts.app')

@section('title', 'Sales Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Sales Report</h1>
            <p class="text-xs text-slate-500">Performance overview for {{ $data['sale_summary']['display_period'] ?? now()->format('F Y') }}</p>
        </div>
        <div>
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $month }}"
                    class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                <button type="submit" class="inline-flex items-center justify-center gap-1 px-3 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg shadow-sm hover:bg-slate-900 transition">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Filter</span>
                </button>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">This Month</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['sale_summary']['this_month'] }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ ($data['sale_summary']['growth_percentage'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ ($data['sale_summary']['growth_percentage'] ?? 0) >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($data['sale_summary']['growth_percentage'] ?? 0), 2) }}%
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Cash Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['cash_sale']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['cash_sale']['count'] }} orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 shrink-0">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Online Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['online_sale']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['online_sale']['count'] }} orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Due Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['due_sale']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['due_sale']['count'] }} orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="chart-line" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Avg / Active Day</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['average_sale']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['average_sale']['count'] }} active days</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Trend Chart --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3">Daily Sales (Last 30 Days)</h2>
        <div class="h-56">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Top Selling Items --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Selling Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-2.5">Product</th>
                            <th class="px-4 py-2.5 text-right">Qty</th>
                            <th class="px-4 py-2.5 text-right">Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['top_selling_items'] as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $item->product_name }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($item->total_quantity) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ money($item->total_sales) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Daily Sales Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Daily Sales Breakdown</h2>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500 sticky top-0 bg-white">
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5 text-right">Orders</th>
                            <th class="px-4 py-2.5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['daily_sales'] as $day)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 text-slate-600">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($day->count) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ money($day->amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;
        const raw = @json($data['sales_chart'] ?? []);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: raw.map(r => r.date),
                datasets: [{
                    label: 'Sales',
                    data: raw.map(r => r.amount),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', callback: v => '৳' + v } }
                }
            }
        });
    });
</script>
@endpush
