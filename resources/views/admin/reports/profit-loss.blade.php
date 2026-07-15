@extends('admin.layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Profit & Loss</h1>
            <p class="text-xs text-slate-500">Financial summary for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Net Profit</p>
                    <p class="text-base font-extrabold text-{{ (float) $netProfit >= 0 ? 'emerald' : 'rose' }}-600 tracking-tight">{{ $data['net_profit'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="percent" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Profit Margin</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['profit_margin'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 shrink-0">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Sales</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['total_sales'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Expenses</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['total_expenses'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Graph --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3">Monthly Sales (This Year)</h2>
        <div class="h-56">
            <canvas id="salesGraph"></canvas>
        </div>
    </div>

    {{-- Expense Breakdown --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Expense Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-2.5">Category</th>
                        <th class="px-4 py-2.5 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($data['expense_breakdown'] as $ex)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $ex['category'] }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $ex['amount'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-8 text-center text-slate-400">No expenses</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesGraph');
        if (!ctx) return;
        const raw = @json($data['sales_graph'] ?? []);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: raw.map(r => r.month),
                datasets: [{
                    label: 'Sales',
                    data: raw.map(r => r.amount),
                    backgroundColor: '#6366f1',
                    borderRadius: 4,
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
