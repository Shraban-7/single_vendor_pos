@extends('admin.layouts.app')

@section('title', 'Sales')

@section('content')
    {{-- Header Section --}}
    <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Sales</h1>
            <p class="text-xs text-slate-500">Manage, track, and export customer invoices.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-medium bg-white border border-slate-200 text-slate-600 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Print</span>
            </button>
            <button onclick="exportSales()"
                class="flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-medium text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-700 transition">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    {{-- Compact Filter Controls --}}
    <div class="p-4 mb-4 bg-white border shadow-sm border-slate-200 rounded-xl">
        <form method="GET" action="{{ route('admin.ecommerce-sales.index') }}" class="space-y-3">
            {{-- Status Sub-Navigation Strip --}}
            <div class="flex items-center gap-1 pb-2 overflow-x-auto border-b border-slate-100 no-scrollbar whitespace-nowrap">
                @php
                    $tabs = [
                        'all' => ['label' => 'All Sales', 'color' => 'indigo'],
                        'pending' => ['label' => 'Pending', 'color' => 'amber'],
                        'confirmed' => ['label' => 'Confirmed', 'color' => 'indigo'],
                        'shipped' => ['label' => 'Shipped', 'color' => 'violet'],
                        'delivered' => ['label' => 'Delivered', 'color' => 'emerald'],
                        'cancelled' => ['label' => 'Cancelled', 'color' => 'rose']
                    ];
                    $currentStatus = request('status', 'all');
                @endphp
                @foreach($tabs as $key => $tab)
                    @php
                        $isActive = $currentStatus === $key;
                        $activeClasses = $key === 'all' ? 'bg-slate-900 text-white' : "bg-{$tab['color']}-50 text-{$tab['color']}-700 border border-{$tab['color']}-200/60 font-semibold";
                    @endphp
                    <a href="{{ route('admin.ecommerce-sales.index', array_merge(request()->except('status'), ['status' => $key])) }}"
                        class="px-3 py-1 text-xs rounded-md transition-all {{ $isActive ? $activeClasses : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ $tab['label'] }} <span class="ml-0.5 text-[10px] {{ $isActive ? 'opacity-90' : 'text-slate-400' }}">({{ $statusCounts[$key] }})</span>
                    </a>
                @endforeach
            </div>

            {{-- Compact Multi-Input Grid --}}
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                {{-- Search Bar --}}
                <div class="sm:col-span-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search sale #, customer name..."
                            class="w-full pl-8 pr-3 text-xs transition border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                        <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                    </div>
                </div>

                {{-- Payment Status Dropdown --}}
                <div class="sm:col-span-2">
                    <select name="payment_status" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                        <option value="all">All Payments</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                {{-- Payment Method Dropdown --}}
                <div class="sm:col-span-2">
                    <select name="payment_method" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                        <option value="all">All Gateways</option>
                        <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
                        <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    </select>
                </div>

                {{-- Date Pickers --}}
                <div class="sm:col-span-1.5 flex items-center gap-1">
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-1.5 text-[11px] border h-9 border-slate-200 rounded-lg focus:outline-none bg-slate-50/50" title="From Date">
                </div>
                <div class="sm:col-span-1.5 flex items-center gap-1">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-1.5 text-[11px] border h-9 border-slate-200 rounded-lg focus:outline-none bg-slate-50/50" title="To Date">
                </div>

                {{-- Action Pipeline --}}
                <div class="flex gap-1 sm:col-span-1">
                    <button type="submit" class="flex items-center justify-center flex-1 text-xs text-white transition rounded-lg shadow-sm h-9 bg-slate-800 hover:bg-slate-900" title="Apply Filters">
                        <i data-lucide="funnel" class="w-3.5 h-3.5"></i>
                    </button>
                    <a href="{{ route('admin.ecommerce-sales.index') }}" class="flex items-center justify-center transition bg-white border rounded-lg shadow-sm w-9 h-9 text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-800" title="Reset">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Clean Data-Dense Table --}}
    <div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Sale Number</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Payment Info</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            {{-- Sale Id --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium">
                                <a href="{{ route('admin.ecommerce-sales.show', $sale->id) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $sale->order_number }}
                                </a>
                                <span class="text-[10px] text-slate-400 block">ID: #{{ $sale->id }}</span>
                            </td>
                            {{-- Customer --}}
                            <td class="px-4 py-2.5 max-w-[150px] truncate">
                                <span class="block font-medium text-slate-800">{{ $sale->shipping_name }}</span>
                                <span class="text-[10px] text-slate-400 block tracking-tight">{{ $sale->shipping_phone }}</span>
                            </td>
                            {{-- Items count --}}
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                {{ $sale->items->count() }} {{ Str::plural('item', $sale->items->count()) }}
                            </td>
                            {{-- Total amount --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-bold text-slate-900 text-sm">
                                {{ money($sale->total) }}
                            </td>
                            {{-- Payment details --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-slate-500">{{ $sale->payment_method->label() }}</span>
                                    @if ($sale->payment_status->value === 'paid')
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Paid</span>
                                    @else
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-600 border border-amber-100">{{ $sale->payment_status->label() }}</span>
                                    @endif
                                </div>
                            </td>
                            {{-- Fullfillment status badge --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'confirmed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'shipped' => 'bg-violet-50 text-violet-700 border-violet-100',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $statusColors[$sale->status->value] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ $sale->status->label() }}
                                </span>
                            </td>
                            {{-- Custom timestamp fields --}}
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                <span>{{ $sale->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $sale->created_at->format('h:i A') }}</span>
                            </td>
                            {{-- Clean inline icon buttons --}}
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('admin.ecommerce-sales.show', $sale->id) }}" class="p-1 transition rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100" title="View">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <button onclick="printSale({{ $sale->id }})" class="p-1 transition rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="Print Invoice">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-xs mx-auto">
                                    <p class="font-semibold text-slate-800">No sales matching parameters found.</p>
                                    <a href="{{ route('admin.ecommerce-sales.index') }}" class="inline-block mt-2 text-xs text-indigo-600 underline">Reset Search</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Light Footer Pagination --}}
        @if ($sales->hasPages())
            <div class="px-4 py-3 text-xs border-t bg-slate-50/50 border-slate-100">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function printSale(saleId) {
                window.open(`/admin/ecommerce-sales/${saleId}/print`, '_blank');
            }

            function exportSales() {
                const params = new URLSearchParams(window.location.search);
                params.append('export', 'csv');
                window.location.href = `{{ route('admin.ecommerce-sales.index') }}?${params.toString()}`;
            }
        </script>
    @endpush
@endsection
