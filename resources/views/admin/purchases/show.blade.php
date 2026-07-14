@extends('admin.layouts.app')
@section('title', 'Purchase Details')

@section('content')
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Purchase #{{ $purchase->purchase_number }}</h1>
        <p class="text-xs text-slate-500">{{ $purchase->purchase_date->format('F d, Y') }} • {{ $purchase->supplier->name ?? 'Unknown Supplier' }}</p>
    </div>
    <div class="flex gap-2">
        @if($purchase->due_amount > 0)
        <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
            <i data-lucide="banknote" class="w-3.5 h-3.5"></i> Make Payment
        </button>
        @endif
        <a href="{{ route('admin.purchases.edit', $purchase->id) }}" class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
            <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
        </a>
        <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back
        </a>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        {{-- Items Table --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Purchased Items</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/70 text-[11px] font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2 text-right">Qty</th>
                        <th class="px-4 py-2 text-right">Unit Price</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->items as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $item->name }} <span class="text-slate-400 font-normal">({{ $item->unit->short_name ?? 'pcs' }})</span></td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ money($item->unit_price) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ money($item->quantity * $item->unit_price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/50 font-semibold text-slate-700">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal:</td>
                        <td class="px-4 py-3 text-right">{{ money($purchase->subtotal) }}</td>
                    </tr>
                    <tr class="text-rose-600">
                        <td colspan="3" class="px-4 py-3 text-right">Due Amount:</td>
                        <td class="px-4 py-3 text-right">{{ money($purchase->due_amount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Payment History --}}
        @if($purchase->payments->count() > 0)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Payment History</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/70 text-[11px] font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Method</th>
                        <th class="px-4 py-2">Note</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->payments as $payment)
                    <tr>
                        <td class="px-4 py-3">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') : $payment->created_at->format('d M, Y') }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->notes ?: '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ money($payment->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Summary</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Supplier</span><span class="font-medium text-slate-800">{{ $purchase->supplier->name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Purchase Date</span><span class="font-medium text-slate-800">{{ $purchase->purchase_date->format('d M, Y') }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Total Paid</span><span class="font-medium text-emerald-600">{{ money($purchase->paid_amount) }}</span></div>
                <div class="flex justify-between border-t border-slate-100 pt-2 mt-2">
                    <span class="font-semibold text-slate-700">Net Due</span>
                    <span class="font-bold text-rose-600">{{ money($purchase->due_amount) }}</span>
                </div>
            </div>
            @if($purchase->notes)
            <div class="pt-2 border-t border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Notes</p>
                <p class="text-xs text-slate-600">{{ $purchase->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Payment Modal --}}
@if($purchase->due_amount > 0)
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div onclick="document.getElementById('paymentModal').classList.add('hidden')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-5 z-10">
            <h3 class="text-base font-bold text-slate-900 mb-4">Make Payment</h3>
            <form action="{{ route('admin.purchases.make-payment', $purchase->id) }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block mb-1 font-semibold text-slate-600">Amount (Max: {{ money($purchase->due_amount) }})</label>
                    <input type="number" step="0.01" name="amount" max="{{ $purchase->due_amount }}" required class="w-full h-9 px-3 border border-slate-200 rounded-lg focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-slate-600">Payment Method</label>
                    <select name="payment_method" required class="w-full h-9 px-3 border border-slate-200 rounded-lg focus:ring-1 focus:ring-indigo-500">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="mobile_banking">Mobile Banking</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-semibold text-slate-600">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="px-3 h-9 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200">Cancel</button>
                    <button type="submit" class="px-3 h-9 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
