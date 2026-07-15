@extends('admin.layouts.app')

@section('title', 'Stock Report')

@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Stock Report</h1>
            <p class="text-xs text-slate-500">Current inventory position across your catalog</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Products</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['stock_report']['product_count'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ $data['stock_report']['category_count'] }} categories</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">In Stock</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['in_stock']['product_count'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ number_format($data['in_stock']['stock_count']) }} units</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Low Stock</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['low_stock']['product_count'] }}</p>
                    <p class="text-[10px] text-slate-400">{{ number_format($data['low_stock']['stock_count']) }} units</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Out of Stock</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ $data['out_of_stock']['product_count'] }}</p>
                    <p class="text-[10px] text-slate-400">needs restock</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Stock Alert List --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Low & Out of Stock Alerts</h2>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500 sticky top-0 bg-white">
                            <th class="px-4 py-2.5">Product</th>
                            <th class="px-4 py-2.5">SKU</th>
                            <th class="px-4 py-2.5 text-right">Stock</th>
                            <th class="px-4 py-2.5 text-right">Alert At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['stock_alert_list'] as $p)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $p['product_name'] }}</td>
                                <td class="px-4 py-2.5 text-slate-500">{{ $p['sku'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ $p['stock_quantity'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ $p['stock_alert_quantity'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">All stock healthy</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Category Stock Breakdown --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Stock Value by Category</h2>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500 sticky top-0 bg-white">
                            <th class="px-4 py-2.5">Category</th>
                            <th class="px-4 py-2.5 text-right">Products</th>
                            <th class="px-4 py-2.5 text-right">Units</th>
                            <th class="px-4 py-2.5 text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($data['category_stock_breakdown'] as $c)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $c['name'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ $c['product_count'] }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($c['stock_count']) }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ $c['stock_value'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Total Stock Value Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-xl p-5 shadow-sm text-white flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-indigo-100 uppercase tracking-wider">Total Inventory Value</p>
            <p class="text-2xl font-extrabold tracking-tight mt-1">{{ $data['stock_report']['stock_value'] }}</p>
        </div>
        <i data-lucide="boxes" class="w-10 h-10 text-indigo-200"></i>
    </div>
</div>

@endsection
