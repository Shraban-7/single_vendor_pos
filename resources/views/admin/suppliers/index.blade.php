@extends('admin.layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Suppliers</h1>
        <p class="text-xs text-slate-500">Manage your vendor relationships, balances, and purchase history.</p>
    </div>
    <div>
        <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Supplier</span>
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5 text-xs">
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Suppliers</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Active Suppliers</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Total Due Amount</p>
        <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ money($stats['due']) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Suppliers with Due</p>
        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['due_receivers'] }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Phone, Company..." class="w-full px-3 h-9 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div class="w-40">
            <label class="block text-[10px] font-semibold text-slate-500 mb-1">Filter</label>
            <select name="filter" class="w-full px-3 h-9 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">All Suppliers</option>
                <option value="due" {{ request('filter') == 'due' ? 'selected' : '' }}>Due Balance</option>
                <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
            </select>
        </div>
        <button type="submit" class="h-9 px-4 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition">Filter</button>
        <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center justify-center h-9 px-4 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3 text-right">Current Balance</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($supplier->image)
                                    <img src="{{ asset('storage/' . $supplier->image) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200">
                                        {{ substr($supplier->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $supplier->name }}</p>
                                    @if($supplier->company_name)
                                        <p class="text-[10px] text-slate-500">{{ $supplier->company_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-slate-700">{{ $supplier->phone ?: 'N/A' }}</p>
                            @if($supplier->email)
                                <p class="text-[10px] text-slate-400">{{ $supplier->email }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-600">{{ $supplier->supplier_code }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $supplier->current_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ money($supplier->current_balance) }}
                        </td>
                        <td class="px-4 py-3">
                            @if($supplier->is_active)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="p-1.5 rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                            No suppliers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
        <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection
