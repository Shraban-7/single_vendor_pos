@extends('admin.layouts.app')

@section('title', 'Exchange Details')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Exchange #{{ $saleReturn->return_number }}</h1>
        <p class="text-xs font-medium text-slate-500 mt-0.5">
            Originating Invoice: <span class="font-mono text-slate-700 font-semibold">{{ $saleReturn->order_number ?? 'N/A' }}</span>
        </p>
    </div>
    <div>
        <a href="{{ route('admin.saleExchanges.index') }}"
           class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Exchanges</span>
        </a>
    </div>
</div>

{{-- Stock Metrics Overview Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-xs">
    {{-- Exchange Value --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Exchange Value</p>
                <p class="text-2xl font-extrabold text-violet-600 tracking-tight mt-0.5">{{ money($saleReturn->exchange_value) }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Total value of exchanged products</p>
            </div>
            <div class="w-9 h-9 bg-violet-50 border border-violet-100 rounded-lg flex items-center justify-center text-violet-600">
                <i data-lucide="repeat" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Refund Offset --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Refund Offset</p>
                <p class="text-2xl font-extrabold text-rose-600 tracking-tight mt-0.5">{{ money($saleReturn->refund_amount) }}</p>
                <p class="text-[10px] text-slate-400 mt-1">{{ ucfirst(str_replace('_', ' ', $saleReturn->refund_method ?? '')) }}</p>
            </div>
            <div class="w-9 h-9 bg-rose-50 border border-rose-100 rounded-lg flex items-center justify-center text-rose-600">
                <i data-lucide="banknote" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Items Exchanged --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Items Exchanged</p>
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $saleReturn->exchangeItems->count() }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Total package pieces processed</p>
            </div>
            <div class="w-9 h-9 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                <i data-lucide="package" class="w-4 h-4"></i>
            </div>
        </div>
    </div>
</div>

{{-- Customer / Sale Information Meta Block --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-4 text-xs">
    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5 mb-3">Customer & Sale Information</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Account Name</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $saleReturn->customer->name ?? ($saleReturn->sale?->customer?->name ?? 'Walk-in Customer') }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Contact Phone</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5 font-mono">{{ $saleReturn->customer->phone ?? ($saleReturn->sale?->customer?->phone ?? '—') }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Processed By</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $saleReturn->employee->name ?? 'System' }}</p>
        </div>
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Transaction Timestamp</p>
            <p class="font-semibold text-slate-800 text-sm mt-0.5">
                {{ $saleReturn->created_at->format('M d, Y • h:i A') }}
            </p>
        </div>
        @if($saleReturn->sale)
        <div>
            <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Origin Invoice</p>
            <a href="{{ route('admin.ecommerce-sales.show', $saleReturn->sale->invoice_number) }}"
               class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline text-sm mt-0.5 inline-block">
                {{ $saleReturn->order_number }}
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Returned Items Matrix Ledger (against which exchange was made) --}}
@if($saleReturn->items->count() > 0)
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-4">
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
                @foreach($saleReturn->items as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-xs block truncate max-w-[280px]">{{ $item->product_name }}</span>
                                    @if($item->variant_name)
                                    <span class="text-[10px] text-slate-500">{{ $item->variant_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $item->quantity }} units
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono">
                            {{ money($item->unit_price) }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap font-bold text-slate-900 font-mono">
                            {{ money($item->quantity * $item->unit_price) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Exchanged Items Matrix Ledger --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 bg-violet-50/40">
        <h2 class="text-xs font-bold uppercase tracking-wider text-violet-500">Exchanged Catalog Items</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Quantity</th>
                    <th class="px-4 py-3">Unit Price</th>
                    <th class="px-4 py-3">Cost Price</th>
                    <th class="px-4 py-3">Exchange Value</th>
                    <th class="px-4 py-3">Line Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @foreach($saleReturn->exchangeItems as $exItem)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-2">
                            <span class="font-semibold text-slate-800 text-xs block truncate max-w-[280px]">{{ $exItem->name }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $exItem->quantity }} units
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono">
                            {{ money($exItem->unit_price) }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono">
                            {{ money($exItem->cost_price) }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-violet-600 font-medium font-mono">
                            {{ money($exItem->exchange_value) }}
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap font-bold text-slate-900 font-mono">
                            {{ money($exItem->subtotal) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-slate-200 bg-slate-50/50 text-xs font-semibold">
                    <td colspan="5" class="px-4 py-2.5 text-right text-slate-500 uppercase tracking-wider text-[10px]">Total Exchange Value</td>
                    <td class="px-4 py-2.5 whitespace-nowrap font-bold text-violet-600 font-mono">{{ money($saleReturn->exchange_value) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Audit Remarks Notes --}}
@if($saleReturn->remarks)
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mt-4 text-xs">
    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5 mb-2">Internal Processing Remarks</h2>
    <p class="text-slate-600 leading-relaxed">{{ $saleReturn->remarks }}</p>
</div>
@endif

@endsection
