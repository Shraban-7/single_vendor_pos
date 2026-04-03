@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')

{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
        <p class="text-sm text-gray-500 mt-1">Manage and track all customer orders</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span class="hidden sm:inline">Print</span>
        </button>
        <button onclick="exportOrders()" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-download"></i>
            <span class="hidden sm:inline">Export</span>
        </button>
    </div>
</div>

{{-- Filters & Search --}}
<div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                All ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Pending ({{ $statusCounts['pending'] }})
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'confirmed'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Confirmed ({{ $statusCounts['confirmed'] }})
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'shipped'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'shipped' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Shipped ({{ $statusCounts['shipped'] }})
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'delivered'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Delivered ({{ $statusCounts['delivered'] }})
            </a>
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Cancelled ({{ $statusCounts['cancelled'] }})
            </a>
        </div>

        {{-- Search & Advanced Filters --}}
        <div class="grid md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order #, customer name, or phone..." class="w-full h-11 pl-10 pr-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- Payment Status --}}
            <div>
                <select name="payment_status" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Payment Status</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            {{-- Payment Method --}}
            <div>
                <select name="payment_method" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Payment Methods</option>
                    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                    <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                    <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                </select>
            </div>
        </div>

        {{-- Date Range --}}
        <div class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 h-11 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.orders.index') }}" class="h-11 px-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shopping-bag text-blue-600"></i>
                            </div>
                            <div>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-semibold text-blue-600 hover:text-blue-800">
                                    {{ $order->order_number }}
                                </a>
                                <p class="text-xs text-gray-500">ID: #{{ $order->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->shipping_phone }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">{{ $order->items->count() }} item(s)</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-base font-bold text-gray-900">{{ money($order->total) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 w-fit">
                                {{ $order->payment_method->label() }}
                            </span>
                            @if($order->payment_status->value === 'paid')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 w-fit">
                                <i class="fas fa-check-circle text-xs"></i>
                                Paid
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 w-fit">
                                <i class="fas fa-clock text-xs"></i>
                                {{ $order->payment_status->label() }}
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'shipped' => 'bg-purple-100 text-purple-700',
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        $statusIcons = [
                        'pending' => 'fa-clock',
                        'confirmed' => 'fa-check',
                        'shipped' => 'fa-truck',
                        'delivered' => 'fa-box-open',
                        'cancelled' => 'fa-times-circle',
                        ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColors[$order->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                            <i class="fas {{ $statusIcons[$order->status->value] ?? 'fa-circle' }}"></i>
                            {{ $order->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="printOrder({{ $order->id }})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-gray-400 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900">No orders found</p>
                                <p class="text-sm text-gray-500">Try adjusting your filters</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="border-t border-gray-200 px-6 py-4">
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