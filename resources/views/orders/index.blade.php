@extends('customer.layout')
@section('title', 'Orders')

@section('dashboard-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
        </div>
    </div>
    <div class="space-y-4">
        @if ($orders->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9a2 2 0 012-2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Orders Yet</h3>
            <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
            <a href="/" class="inline-block bg-brand-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Continue Shopping</a>
        </div>
        @else
        <!-- Orders Grid for Mobile & Table for Desktop -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Order ID</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Items</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900">#{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y h:i a') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items_count ?? ($order->items ? (is_countable($order->items) ? count($order->items) : 0) : 0) }} item(s)</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold text-{{ $order->status->color() }}-600 bg-{{ $order->status->color() }}-100">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">{{ money($order->total) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('orders.show', $order->order_number) }}" class="inline-flex items-center px-4 py-2 bg-brand-blue text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
            @foreach ($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide mb-1">Order</p>
                        <p class="font-bold text-lg text-gray-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold text-{{ $order->status->color() }}-600 bg-{{ $order->status->color() }}-100">
                        {{ $order->status->label() }}
                    </span>
                </div>

                <div class="space-y-3 mb-6 pb-6 border-b border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Date</span>
                        <span class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Items</span>
                        <span class="font-medium text-gray-900">{{ $order->items_count ?? ($order->items ? (is_countable($order->items) ? count($order->items) : 0) : 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total</span>
                        <span class="font-bold text-lg text-gray-900">{{ money($order->total) }}</span>
                    </div>
                </div>

                <a href="{{ route('orders.show', $order->order_number) }}" class="block w-full bg-brand-blue text-white py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition">
                    View Details
                </a>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection