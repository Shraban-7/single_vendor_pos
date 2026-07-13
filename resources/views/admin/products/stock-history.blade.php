@extends('admin.layouts.app')
@section('title', 'Stock History')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Stock History</h1>
        <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $product->name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.manage-stock', $product) }}"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100/60 transition">
            <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
            <span>Manage Stock</span>
        </a>
        <a href="{{ route('admin.products.edit', $product) }}"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Edit</span>
        </a>
    </div>
</div>

{{-- Stock Metrics Overview Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-xs">
    {{-- Balance Card --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Current Balance</p>
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $product->stock_in }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Available operational warehouse reserves</p>
            </div>
            <div class="w-9 h-9 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Log In Card --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Restocked</p>
                <p class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-0.5">
                    {{ $stockLogs->where('type', 'in')->sum('quantity') }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Gross increments logged inbound</p>
            </div>
            <div class="w-9 h-9 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Log Out Card --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Dispatched</p>
                <p class="text-2xl font-extrabold text-rose-600 tracking-tight mt-0.5">
                    {{ $stockLogs->where('type', 'out')->sum('quantity') }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Gross decrements logged outbound</p>
            </div>
            <div class="w-9 h-9 bg-rose-50 border border-rose-100 rounded-lg flex items-center justify-center text-rose-600">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </div>
        </div>
    </div>
</div>

{{-- Stock Ledger Registry --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaction Audit Ledger</h2>
    </div>

    @if($stockLogs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Log Type</th>
                        <th class="px-4 py-3">Variance delta</th>
                        <th class="px-4 py-3">Ledger Delta Bounds</th>
                        <th class="px-4 py-3">Operator Context</th>
                        <th class="px-4 py-3">Audit Reference Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($stockLogs as $log)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- Timestamp --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="font-medium text-slate-700 block">{{ $log->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $log->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Operational Status Flag --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($log->type === 'in')
                                    <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Stock In</span>
                                @else
                                    <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-700 border border-rose-100/70">Stock Out</span>
                                @endif
                            </td>

                            {{-- Delta Offset Weight --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-bold text-sm">
                                <span class="{{ $log->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $log->type === 'in' ? '+' : '–' }}{{ $log->quantity }}
                                </span>
                            </td>

                            {{-- Timeline Tracking Vector --}}
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                                <span class="text-slate-400">{{ $log->stock_before }}</span>
                                <i data-lucide="move-right" class="w-3 h-3 inline mx-1 text-slate-300"></i>
                                <span class="font-semibold text-slate-800">{{ $log->stock_after }}</span>
                            </td>

                            {{-- Identity Vector Metrics --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="font-medium text-slate-800 block">{{ $log->user->name }}</span>
                                <span class="text-[10px] text-slate-400 block tracking-tight mt-0.5">{{ $log->user->email }}</span>
                            </td>

                            {{-- Ledger Explanatory Context --}}
                            <td class="px-4 py-2.5 max-w-xs truncate text-slate-500">
                                @if($log->note)
                                    <span class="block truncate" title="{{ $log->note }}">{{ $log->note }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Strip --}}
        @if($stockLogs->hasPages())
            <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100 text-xs text-slate-600">
                {{ $stockLogs->links() }}
            </div>
        @endif
    @else
        {{-- Clean Blank Workspace Illustration State --}}
        <div class="p-12 text-center text-slate-500">
            <div class="max-w-xs mx-auto flex flex-col items-center">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 mb-3 border border-slate-100">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-slate-900">No operational stock records</h3>
                <p class="text-xs text-slate-500 mt-0.5 mb-4">There are no documented ledger increments tracked for this single storage item variant base catalog catalog unit.</p>
                <a href="{{ route('admin.products.manage-stock', $product) }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 h-8 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add First Entry</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
