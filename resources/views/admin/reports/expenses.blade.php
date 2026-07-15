@extends('admin.layouts.app')

@section('title', 'Expense Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Expense Report</h1>
            <p class="text-xs text-slate-500">Expense overview for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Expenses</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['expense_summary']['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Transactions</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['expense_summary']['transaction_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">MoM Growth</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['expense_summary']['growth_percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Category Expenses --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Expenses by Category</h2>
            </div>
            <div class="p-4 space-y-3">
                @forelse($data['category_expenses'] as $cat)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $cat['name'] }}</span>
                            <span class="font-semibold text-slate-900">{{ $cat['amount'] }} <span class="text-slate-400 font-normal">({{ $cat['percentage'] }}%)</span></span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400 text-xs py-6">No expenses this period</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Recent Expenses</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-2.5">Title</th>
                            <th class="px-4 py-2.5">Category</th>
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['recent_expenses'] as $e)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $e['title'] }}</td>
                                <td class="px-4 py-2.5 text-slate-500">{{ $e['category'] }}</td>
                                <td class="px-4 py-2.5 text-slate-500">{{ $e['date'] }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $e['amount'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No expenses</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
