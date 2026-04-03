@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Success Message --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-full mb-4 animate-bounce">
                <i class="fas fa-check text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600 text-lg">Thank you for your purchase</p>
        </div>

        {{-- Order Details Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
            {{-- Order Number & Status --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-200">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Order Number</p>
                    <h2 class="text-2xl font-bold text-brand-blue">{{ $order->order_number }}</h2>
                </div>
                <div class="flex items-center gap-2 bg-yellow-50 px-4 py-2 rounded-full">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-yellow-700">{{ $order->status->label() }}</span>
                </div>
            </div>

            {{-- Order Info --}}
            <div class="grid md:grid-cols-2 gap-6 py-6 border-b border-gray-200">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-brand-blue"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Name:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
                        @if($order->shipping_email)
                        <p><strong>Email:</strong> {{ $order->shipping_email }}</p>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-brand-blue"></i>
                        Delivery Address
                    </h3>
                    <div class="text-sm text-gray-600">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_district }}</p>
                        <p class="mt-2">
                            <span class="inline-flex items-center gap-1 bg-blue-50 text-brand-blue px-2 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-truck"></i>
                                {{ $order->delivery_zone->label() }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="py-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-box text-brand-blue"></i>
                    Order Items ({{ $order->items->count() }})
                </h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex gap-4">
                        <div class="w-20 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 mb-1">{{ $item->product_name }}</h4>
                            @if($item->size_name || $item->color_name)
                            <p class="text-xs text-gray-500 mb-2">
                                @if($item->size_name)Size: {{ $item->size_name }}@endif
                                @if($item->size_name && $item->color_name) | @endif
                                @if($item->color_name)Color: {{ $item->color_name }}@endif
                            </p>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Qty: {{ $item->quantity }}</span>
                                <span class="font-semibold text-gray-900">{{ money($item->subtotal) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Payment & Price Summary --}}
            <div class="py-6">
                <div class="flex flex-col md:flex-row gap-6">
                    {{-- Payment Info --}}
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-credit-card text-brand-blue"></i>
                            Payment Information
                        </h3>
                        <div class="space-y-2 text-sm">
                            <p>
                                <strong>Method:</strong>
                                <span class="ml-2 inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs font-medium">
                                    {{ $order->payment_method->label() }}
                                </span>
                            </p>
                            <p>
                                <strong>Status:</strong>
                                <span class="ml-2 inline-flex items-center gap-1 {{ $order->payment_status->value === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} px-2 py-1 rounded text-xs font-medium">
                                    {{ $order->payment_status->label() }}
                                </span>
                            </p>
                            @if($order->transaction_id)
                            <p><strong>Transaction ID:</strong> <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ $order->transaction_id }}</code></p>
                            @endif
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="flex-1 bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">{{ money($order->subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium">{{ money($order->shipping_cost) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-medium">-{{ money($order->discount_amount) }}</span>
                            </div>
                            @endif
                            <div class="h-px bg-gray-300 my-2"></div>
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-brand-blue">{{ money($order->total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->notes)
            {{-- Order Notes --}}
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-brand-blue"></i>
                    Order Notes
                </h3>
                <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        {{-- What's Next Section --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-brand-blue"></i>
                What Happens Next?
            </h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-brand-blue rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Order Confirmation</h4>
                        <p class="text-sm text-gray-600">We'll call you shortly to confirm your order details</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Order Processing</h4>
                        <p class="text-sm text-gray-600">We'll prepare and pack your items carefully</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">Delivery</h4>
                        <p class="text-sm text-gray-600">Your order will be delivered within {{ $order->delivery_zone->value === 'inside_dhaka' ? '1-2 days' : '3-5 days' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 px-8 py-3 bg-brand-blue text-white rounded-xl font-semibold hover:bg-blue-600 transition shadow-lg">
                <i class="fas fa-home"></i>
                Continue Shopping
            </a>
            @auth
            <a href="#" class="flex items-center justify-center gap-2 px-8 py-3 bg-white text-brand-blue border-2 border-brand-blue rounded-xl font-semibold hover:bg-blue-50 transition">
                <i class="fas fa-receipt"></i>
                View My Orders
            </a>
            @endauth
        </div>

        {{-- Support --}}
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>Need help? Contact us at <a href="tel:+8801234567890" class="text-brand-blue hover:underline font-medium">+880 1234-567890</a></p>
        </div>
    </div>
</div>
@endsection