@extends('admin.layouts.app')
@section('title', 'Purchases')

@section('content')
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Purchases</h1>
        <p class="text-xs text-slate-500">Track inventory acquisitions, costs, and supplier dues.</p>
    </div>
    <a href="{{ route('admin.purchases.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
        <span>New Purchase</span>
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5 text-xs">
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Purchase Value</p>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ money($stats['total_subtotal']) }}</p>
        </div>
        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600"><i data-lucide="bar-chart-3" class="w-5 h-5"></i></div>
                </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Outstanding Due</p>
            <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ money($stats['total_due']) }}</p>
        </div>
        <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600"><i data-lucide="alert-circle" class="w-5 h-5"></i></div>
    </div>
</div>

{{-- Filters --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.purchases.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-[10px] font-semibold text-slate-500 mb-1">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="h-9 px-3 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-[10px] font-semibold text-slate-500 mb-1">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="h-9 px-3 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Supplier</label>
            <select name="supplier_id" class="h-9 px-3 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="h-9 px-4 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition">Filter</button>
        <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center justify-center h-9 px-4 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Purchase No.</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Due</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($purchases as $purchase)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-3 font-mono text-slate-700">{{ $purchase->purchase_number }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $purchase->purchase_date->format('d M, Y') }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ money($purchase->subtotal) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $purchase->due_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ money($purchase->due_amount) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($purchase->due_amount <= 0)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Paid</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 border border-amber-100">Due</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="p-1.5 rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                                <a href="{{ route('admin.purchases.edit', $purchase->id) }}" class="p-1.5 rounded text-slate-400 hover:text-emerald-600 hover:bg-slate-100 transition" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                                <form action="{{ route('admin.purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Delete this purchase? Stock will be reverted.');">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
        <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection
