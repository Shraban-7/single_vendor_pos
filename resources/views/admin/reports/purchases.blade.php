@extends('admin.layouts.app')

@section('title', 'Purchase Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Purchase Report</h1>
            <p class="text-xs text-slate-500">Procurement overview for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
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
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Paid</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['paid_purchase']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['paid_purchase']['count'] }} orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Unpaid (Due)</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['unpaid_purchase']['amount'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['unpaid_purchase']['count'] }} orders</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Active Suppliers</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['supplier_count']['active'] }}</p>
                    <p class="text-[10px] text-slate-400">of {{ $data['supplier_count']['total'] }} total</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-violet-50 text-violet-600 border border-violet-100 shrink-0">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Top Items</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['top_purchasing_items']->count() }}</p>
                    <p class="text-[10px] text-slate-400">distinct products</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Top Suppliers --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Suppliers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-2.5">Supplier</th>
                            <th class="px-4 py-2.5 text-right">Orders</th>
                            <th class="px-4 py-2.5 text-right">Purchases</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['top_suppliers'] as $s)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $s['name'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($s['count']) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $s['amount'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Purchasing Items --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Purchasing Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-2.5">Product</th>
                            <th class="px-4 py-2.5 text-right">Qty</th>
                            <th class="px-4 py-2.5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['top_purchasing_items'] as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $item->name }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($item->total_quantity) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ money($item->total_purchases) }}</td>
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
