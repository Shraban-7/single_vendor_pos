@extends('admin.layouts.app')

@section('title', 'Return Details')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Return #{{ $return->return_number }}</h1>
        <p class="text-xs font-medium text-slate-500 mt-0.5">
            Originating Order: <span class="font-mono text-slate-700 font-semibold">{{ $return->sale->order_number ?? 'N/A' }}</span>
        </p>
    </div>
    <div>
        <a href="{{ route('admin.saleReturns.index') }}"
           class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Returns</span>
        </a>
    </div>
</div>

{{-- Stock Metrics Overview Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-xs">
    {{-- Refund Amount --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Refund Total</p>
                <p class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-0.5">{{ money($return->refund_amount) }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Reimbursed financial allocation</p>
            </div>
            <div class="w-9 h-9 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                <i data-lucide="banknote" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Refund Method --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Refund Method</p>
                <p class="text-xl font-extrabold text-slate-900 tracking-tight mt-1">{{ ucfirst($return->refund_method) }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Settlement dispatch pipeline channel</p>
            </div>
            <div class="w-9 h-9 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Items Returned --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Items Volume</p>
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $return->items->count() }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Total package pieces processed</p>
            </div>
            <div class="w-9 h-9 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
        </div>
    </div>
</div>

{{-- Customer Information Meta Block --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-4 text-xs">
    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5 mb-3">Customer Information</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Account Name</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $return->customer_name }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Contact Phone</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5 font-mono">{{ $return->customer_phone }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Transaction Timestamp</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5">
                {{ $return->created_at->format('M d, Y • h:i A') }}
            </p>
        </div>
    </div>
</div>

{{-- Returned Items Matrix Ledger --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Returned Catalog Items</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Product Catalog Item</th>
                    <th class="px-4 py-3">Quantity</th>
                    <th class="px-4 py-3">Unit Valuation</th>
                    <th class="px-4 py-3">Gross Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @foreach($return->items as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        {{-- Identity Meta --}}
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded border border-slate-200 bg-slate-50 flex-shrink-0 overflow-hidden">
                                    <img src="{{ $item->product_image }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-xs block truncate max-w-[280px]">{{ $item->product_name }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Quantity --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $item->quantity }} units
                        </td>

                        {{-- Unit Price --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono">
                            {{ money($item->unit_price) }}
                        </td>

                        {{-- Line Total --}}
                        <td class="px-4 py-2.5 whitespace-nowrap font-bold text-slate-900 font-mono">
                            {{ money($item->quantity * $item->unit_price) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Audit Remarks Notes --}}
@if($return->remarks)
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mt-4 text-xs">
    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5 mb-2">Internal Processing Remarks</h2>
    <p class="text-slate-600 leading-relaxed">{{ $return->remarks }}</p>
</div>
@endif

@endsection
