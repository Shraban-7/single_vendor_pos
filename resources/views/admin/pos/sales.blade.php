@extends('admin.layouts.app')

@section('title', 'POS Sales')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">POS Sales</h1>
        <p class="text-xs text-slate-500">Manage POS sales, drafts & completed orders</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
            <span class="hidden sm:inline">Print</span>
        </button>
        <button onclick="exportPosSales()"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="download" class="w-3.5 h-3.5"></i>
            <span class="hidden sm:inline">Export</span>
        </button>
    </div>
</div>

{{-- Compact Filter Framework --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.sales.index') }}" class="space-y-2.5">

        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-1.5 pb-2.5 border-b border-slate-100">
            <a href="{{ route('admin.sales.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                class="px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                   {{ request('status', 'all') === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All ({{ $statusCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.sales.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}"
                class="px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                   {{ request('status') === 'draft' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Draft ({{ $statusCounts['draft'] ?? 0 }})
            </a>
            <a href="{{ route('admin.sales.index', array_merge(request()->except('status'), ['status' => 'delivered'])) }}"
                class="px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                   {{ request('status') === 'delivered' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                Delivered ({{ $statusCounts['delivered'] ?? 0 }})
            </a>
            <a href="{{ route('admin.sales.index', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
                class="px-2.5 py-1 text-[11px] font-semibold rounded-md transition
                   {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                Cancelled ({{ $statusCounts['cancelled'] ?? 0 }})
            </a>
        </div>

        {{-- Search & Filters Grid --}}
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
            <div class="sm:col-span-4">
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by order #, customer name..."
                        class="w-full pl-8 pr-3 text-xs transition border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                    <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                </div>
            </div>
            <div class="sm:col-span-2">
                <select name="payment_status"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="all">All Payment</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <select name="payment_method"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="all">All Methods</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="bkash" {{ request('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                    <option value="nagad" {{ request('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none bg-slate-50/50">
            </div>
            <div class="sm:col-span-1">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none bg-slate-50/50">
            </div>
            <div class="flex gap-1 sm:col-span-1">
                <button type="submit" class="flex items-center justify-center flex-1 text-xs text-white transition rounded-lg shadow-sm h-9 bg-slate-800 hover:bg-slate-900" title="Filter">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                </button>
                <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-center transition bg-white border rounded-lg shadow-sm w-9 h-9 text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-800" title="Clear">
                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Order</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Items</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($sales as $sale)
                    <tr class="transition-colors hover:bg-slate-50/60">
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500">
                                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm block truncate max-w-[180px]">#{{ $sale->order_number ?? $sale->id }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">ID: {{ $sale->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <p class="font-medium text-slate-800">{{ $sale->shipping_name ?? 'Walk-in Customer' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $sale->shipping_phone ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                            {{ $sale->items->count() }} item(s)
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="text-sm font-bold text-slate-900">{{ money($sale->total) }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-amber-50 text-amber-700 border border-amber-200/60',
                                    'delivered' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border border-rose-100/70',
                                ];
                            @endphp
                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded {{ $statusColors[$sale->status->value] ?? 'bg-slate-50 text-slate-600 border border-slate-200/80' }}">
                                {{ $sale->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-medium text-slate-700 block">{{ $sale->created_at->format('M d, Y') }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $sale->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-0.5">
                                <a href="{{ route('admin.sales.show', $sale->order_number) }}"
                                    class="p-1 transition rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100"
                                    title="View">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.pos.index', ['invoice_number' => $sale->invoice_number]) }}"
                                    class="p-1 transition rounded text-slate-400 hover:text-emerald-600 hover:bg-slate-100"
                                    title="Edit">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-slate-500">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                </div>
                                <h3 class="font-bold text-slate-900">No POS sales found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Start selling from the POS to see sales here.</p>
                                <a href="{{ route('admin.pos.index') }}" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:underline">Go to POS</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
        <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
            <div class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-slate-600">
                <div>
                    Showing <span class="font-semibold text-slate-800">{{ $sales->firstItem() }}</span> to <span class="font-semibold text-slate-800">{{ $sales->lastItem() }}</span> of <span class="font-semibold text-slate-800">{{ $sales->total() }}</span> sales
                </div>
                <div class="font-medium">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
                    function exportPosSales() {
        window.location.href = '{{ route("admin.sales.index") }}?export=csv&' + window.location.search.slice(1);
    }

    document.querySelectorAll('#payment_status, #payment_method').forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush

@endsection
