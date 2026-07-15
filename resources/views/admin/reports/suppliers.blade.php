@extends('admin.layouts.app')

@section('title', 'Supplier Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Supplier Report</h1>
            <p class="text-xs text-slate-500">Supplier activity for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
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
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Suppliers</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['supplier_summary']['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">New This Period</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['supplier_summary']['new'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 shrink-0">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Active</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['supplier_summary']['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Due</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['total_due']['amount'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Total Purchases Summary --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Purchases (Period)</p>
            <p class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $data['total_purchases']['amount'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ $data['total_purchases']['count'] }} orders</p>
        </div>

        {{-- Top Suppliers --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden lg:col-span-2">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Top Suppliers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-2.5">Supplier</th>
                            <th class="px-4 py-2.5">Phone</th>
                            <th class="px-4 py-2.5 text-right">Orders</th>
                            <th class="px-4 py-2.5 text-right">Purchases</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['top_suppliers'] as $s)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $s['name'] }}</td>
                                <td class="px-4 py-2.5 text-slate-500">{{ $s['phone'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($s['purchase_count']) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $s['total_purchase'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
