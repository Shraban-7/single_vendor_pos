@extends('customer.layout')
@section('title', 'Dashboard')

@section('dashboard-content')
<!-- Dashboard Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_orders'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-brand-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-brand-blue text-xl"></i>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="text-brand-blue text-sm font-medium mt-4 inline-flex items-center gap-1 hover:gap-2 transition-all">
            View all <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pending Orders</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pending_orders'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="text-yellow-600 text-sm font-medium mt-4 inline-flex items-center gap-1 hover:gap-2 transition-all">
            View pending <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Wishlist -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Wishlist Items</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['wishlist_items'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-heart text-gray-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('customer.wishlist') }}" class="text-gray-600 text-sm font-medium mt-4 inline-flex items-center gap-1 hover:gap-2 transition-all">
            View wishlist <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Saved Addresses -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Saved Addresses</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['addresses'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('customer.addresses') }}" class="text-green-600 text-sm font-medium mt-4 inline-flex items-center gap-1 hover:gap-2 transition-all">
            Manage addresses <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
        <a href="{{ route('orders.index') }}" class="text-brand-blue text-sm font-medium hover:text-brand-blue-700">
            View all orders
        </a>
    </div>

    @if($recent_orders->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Order Number
                    </th>
                    <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($recent_orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">#{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-500 md:hidden">{{ $order->created_at->format('M d, Y') }}</p>
                    </td>
                    <td class="hidden md:table-cell px-6 py-4 text-sm text-gray-600">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-brand-blue-100 text-brand-blue-800',
                        'shipped' => 'bg-gray-100 text-gray-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $statusColors[$order->status->value] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                            {{ ucfirst($order->status->value) }}
                        </span>
                    </td>
                    <td class="hidden sm:table-cell px-6 py-4 text-sm font-semibold text-gray-800">
                        ৳{{ number_format($order->total_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('orders.show', $order->order_number) }}"
                            class="inline-flex items-center gap-1 text-brand-blue hover:text-brand-blue-700 font-medium text-sm">
                            View <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="px-6 py-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
            <i class="fas fa-shopping-bag text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Orders Yet</h3>
        <p class="text-gray-600 mb-6">Start shopping and your orders will appear here.</p>
        <a href="{{ route('products.index') }}"
            class="inline-block bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition">
            Browse Products
        </a>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-6 md:mt-8">
    <a href="{{ route('products.index') }}"
        class="bg-brand-blue text-white rounded-lg p-6 hover:bg-brand-blue-700 transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Continue Shopping</h3>
                <p class="text-sm text-brand-blue-100">Browse new arrivals</p>
            </div>
        </div>
    </a>

    <a href="{{ route('customer.wishlist') }}"
        class="bg-gray-700 text-white rounded-lg p-6 hover:bg-gray-800 transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-heart text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">My Wishlist</h3>
                <p class="text-sm text-gray-100">{{ $stats['wishlist_items'] }} items saved</p>
            </div>
        </div>
    </a>

    <a href="{{ route('orders.index') }}"
        class="bg-gray-700 text-white rounded-lg p-6 hover:bg-gray-800 transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Track Orders</h3>
                <p class="text-sm text-gray-100">View order status</p>
            </div>
        </div>
    </a>
</div>
@endsection