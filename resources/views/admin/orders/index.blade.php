@extends('admin.layouts.app')

@section('title', 'Orders Management')

@section('content')
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 mb-8 sm:mb-10 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Overview, filters, tracking, and customer history management.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()"
                class="flex items-center justify-center gap-2 px-4 text-sm font-medium transition bg-white border shadow-sm h-11 border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i data-lucide="printer" class="w-4 h-4 text-slate-400 group-hover:text-slate-600"></i>
                <span>Print Report</span>
            </button>
            <button onclick="exportOrders()"
                class="flex items-center justify-center gap-2 px-4 text-sm font-medium text-white transition shadow-sm h-11 bg-emerald-600 rounded-xl hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
            </button>
        </div>
    </div>

    {{-- Filters & Search Section --}}
    <div class="p-6 mb-8 bg-white border shadow-sm border-slate-200/80 rounded-2xl">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-6">
            {{-- Status Tabs --}}
            <div>
                <label class="block mb-2 text-xs font-semibold tracking-wider uppercase text-slate-400">Filter By Status</label>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status', 'all') === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        All Orders <span class="ml-1 text-xs opacity-80">({{ $statusCounts['all'] }})</span>
                    </a>
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-sm shadow-amber-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Pending <span class="ml-1 text-xs opacity-80">({{ $statusCounts['pending'] }})</span>
                    </a>
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'confirmed'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status') === 'confirmed' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Confirmed <span class="ml-1 text-xs opacity-80">({{ $statusCounts['confirmed'] }})</span>
                    </a>
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'shipped'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status') === 'shipped' ? 'bg-violet-600 text-white shadow-sm shadow-violet-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Shipped <span class="ml-1 text-xs opacity-80">({{ $statusCounts['shipped'] }})</span>
                    </a>
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'delivered'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status') === 'delivered' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Delivered <span class="ml-1 text-xs opacity-80">({{ $statusCounts['delivered'] }})</span>
                    </a>
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white shadow-sm shadow-rose-100' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Cancelled <span class="ml-1 text-xs opacity-80">({{ $statusCounts['cancelled'] }})</span>
                    </a>
                </div>
            </div>

            <hr class="border-slate-100" />

            {{-- Inputs Grid --}}
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
                {{-- Search --}}
                <div class="sm:col-span-2">
                    <label class="block mb-1.5 text-xs font-semibold tracking-wider uppercase text-slate-400">Search Parameter</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Order #, name, phone..."
                            class="w-full pl-10 pr-4 text-sm transition-all border bg-slate-50 h-11 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                        <i data-lucide="search" class="absolute w-4 h-4 -translate-y-1/2 left-3 top-1/2 text-slate-400"></i>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div>
                    <label class="block mb-1.5 text-xs font-semibold tracking-wider uppercase text-slate-400">Payment Status</label>
                    <select name="payment_status"
                        class="w-full px-4 text-sm transition-all border bg-slate-50 h-11 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                        <option value="all">All Statuses</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block mb-1.5 text-xs font-semibold tracking-wider uppercase text-slate-400">Gateway/Method</label>
                    <select name="payment_method"
                        class="w-full px-4 text-sm transition-all border bg-slate-50 h-11 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                        <option value="all">All Methods</option>
                        <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    </select>
                </div>
            </div>

            {{-- Advanced Meta & Triggers --}}
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold tracking-wider uppercase text-slate-400">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                        class="w-full px-4 text-sm transition-all border bg-slate-50 h-11 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold tracking-wider uppercase text-slate-400">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                        class="w-full px-4 text-sm transition-all border bg-slate-50 h-11 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                </div>

                {{-- Actions Alignment --}}
                <div class="flex items-end gap-2 sm:col-span-2">
                    <button type="submit"
                        class="flex items-center justify-center flex-1 text-sm font-semibold text-white transition-all bg-indigo-600 shadow-sm h-11 rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i data-lucide="funnel" class="w-4 h-4 mr-2"></i>
                        <span>Apply Filters</span>
                    </button>
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center justify-center transition-all bg-white border shadow-sm w-11 h-11 text-slate-600 border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200"
                        title="Reset Filters">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Orders Content Container --}}
    <div class="overflow-hidden bg-white border shadow-sm border-slate-200/80 rounded-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-bold tracking-wider uppercase border-b bg-slate-50/70 border-slate-200 text-slate-500">
                        <th class="px-6 py-4.5">Order Info</th>
                        <th class="px-6 py-4.5">Customer Details</th>
                        <th class="px-6 py-4.5">Items Count</th>
                        <th class="px-6 py-4.5">Grand Total</th>
                        <th class="px-6 py-4.5">Payment Metrics</th>
                        <th class="px-6 py-4.5">Fulfillment Status</th>
                        <th class="px-6 py-4.5">Date Added</th>
                        <th class="px-6 py-4.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="transition-all group hover:bg-slate-50/40">
                            {{-- Order Spec --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-indigo-600 border border-indigo-100 bg-indigo-50 rounded-xl">
                                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="block font-semibold transition-colors text-slate-900 group-hover:text-indigo-600">
                                            {{ $order->order_number }}
                                        </a>
                                        <span class="text-xs font-medium text-slate-400 block mt-0.5">ID: #{{ $order->id }}</span>
                                    </div>
                                </div>
                            </td>
                            {{-- Customer Profile --}}
                            <td class="px-6 py-4.5">
                                <div class="max-w-[180px] truncate">
                                    <p class="font-semibold text-slate-800">{{ $order->shipping_name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5 tracking-tight">{{ $order->shipping_phone }}</p>
                                </div>
                            </td>
                            {{-- Items Count --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                </span>
                            </td>
                            {{-- Total --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <span class="text-base font-bold text-slate-900">{{ money($order->total) }}</span>
                            </td>
                            {{-- Payment Details --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center text-xs font-medium text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                        {{ $order->payment_method->label() }}
                                    </span>
                                    @if ($order->payment_status->value === 'paid')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 w-fit border border-emerald-100">
                                            <i data-lucide="check" class="w-3 h-3"></i> Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 w-fit border border-amber-100">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $order->payment_status->label() }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            {{-- Core Fulfillment Status --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'confirmed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'shipped' => 'bg-violet-50 text-violet-700 border-violet-100',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'clock',
                                        'confirmed' => 'check-circle-2',
                                        'shipped' => 'truck',
                                        'delivered' => 'package',
                                        'cancelled' => 'x-circle',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$order->status->value] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                    <i data-lucide="{{ $statusIcons[$order->status->value] ?? 'help-circle' }}" class="w-3.5 h-3.5"></i>
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            {{-- Date Structure --}}
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div>
                                    <p class="font-medium text-slate-700">{{ $order->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('h:i A') }}</p>
                                </div>
                            </td>
                            {{-- Row Actions --}}
                            <td class="px-6 py-4.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="flex items-center justify-center w-8 h-8 transition rounded-lg text-slate-500 hover:bg-slate-100 hover:text-indigo-600"
                                        title="View Details">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <button onclick="printOrder({{ $order->id }})"
                                        class="flex items-center justify-center w-8 h-8 transition rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800"
                                        title="Print Invoice">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Clean Empty State Illustration --}}
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center max-w-sm mx-auto">
                                    <div class="flex items-center justify-center mb-4 border shadow-inner w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 border-slate-100">
                                        <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900">No matching orders found</h3>
                                    <p class="mt-1 text-sm text-slate-500">We couldn't find any orders matching your combination of keyword filters and parameters.</p>
                                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center px-4 py-2 mt-4 text-xs font-semibold transition-all bg-white border text-slate-700 border-slate-200 rounded-xl hover:bg-slate-50">
                                        Clear Search Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Block --}}
        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t bg-slate-50/50 border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function printOrder(orderId) {
                window.open(`/admin/orders/${orderId}/print`, '_blank');
            }

            function exportOrders() {
                const params = new URLSearchParams(window.location.search);
                params.append('export', 'csv');
                window.location.href = `{{ route('admin.orders.index') }}?${params.toString()}`;
            }
        </script>
    @endpush
@endsection
