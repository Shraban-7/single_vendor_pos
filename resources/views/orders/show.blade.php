@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Back Button & Header -->
        <div class="mb-8">
            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-brand-blue hover:text-blue-700 font-medium mb-4">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Order Number</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">#{{ $order->order_number }}</h1>
                </div>
                <div class="flex items-center gap-2 bg-{{ $order->status->color() }}-100 px-4 py-3 rounded-full">
                    <div class="w-3 h-3 rounded-full animate-pulse" style="background-color: currentColor;"></div>
                    <span class="text-sm font-bold text-{{ $order->status->color() }}-600">{{ $order->status->label() }}</span>
                </div>
            </div>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
            <!-- Order Date & Info -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-200">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Order Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('F d, Y h:i a') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Estimated Delivery</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $order->updated_at->addDays(5)->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Customer & Delivery Info -->
            <div class="grid md:grid-cols-2 gap-6 py-6 border-b border-gray-200">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                        <i class="fas fa-user text-brand-blue text-lg"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Full Name</p>
                            <p class="font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Email</p>
                            <p class="font-medium text-gray-900">{{ $order->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Phone</p>
                            <p class="font-medium text-gray-900">{{ $order->user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                        <i class="fas fa-map-marker-alt text-brand-blue text-lg"></i>
                        Delivery Address
                    </h3>
                    <div class="space-y-3 text-sm text-gray-900">
                        <div>
                            <p class="font-medium">{{ $order->shipping_address ?? $order->user->address->address_line1 ?? 'N/A' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium">{{ $order->shipping_city ?? $order->user->address->city ?? 'N/A' }},</span>
                            <span class="font-medium">{{ $order->shipping_district ?? $order->user->address->district ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="py-6 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <i class="fas fa-box text-brand-blue text-lg"></i>
                    Order Items ({{ $order->items ? count($order->items) : 0 }})
                </h3>
                <div class="space-y-4">
                    @if($order->items && count($order->items) > 0)
                    @foreach($order->items as $item)
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="w-24 h-28 flex-shrink-0 rounded-lg overflow-hidden bg-gray-200">
                            @if($item->product)
                            <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-2xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $item->product->name ?? 'Product' }}</h4>
                            <div class="space-y-1 text-xs text-gray-600 mb-3">
                                @if($item->size)
                                <p><span class="font-medium">Size:</span> {{ $item->size->name ?? 'N/A' }}</p>
                                @endif
                                @if($item->color)
                                <p><span class="font-medium">Color:</span> {{ $item->color->name ?? 'N/A' }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Qty: <strong>{{ $item->quantity }}</strong></span>
                                <span class="text-lg font-bold text-gray-900">{{ money($item->subtotal) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-gray-500 text-center py-4">No items found for this order</p>
                    @endif
                </div>
            </div>

            <!-- Payment & Price Summary -->
            <div class="py-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Payment Info -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                            <i class="fas fa-credit-card text-brand-blue text-lg"></i>
                            Payment Information
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-500 mb-1">Payment Method</p>
                                <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-semibold">
                                    {{ $order->payment_method->label() }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Payment Status</p>
                                @php
                                $paymentStatusColor = $order->payment_status->isPending() ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700';
                                @endphp
                                <span class="inline-block {{ $paymentStatusColor }} px-3 py-1 rounded-lg text-xs font-semibold">
                                    {{ $order->payment_status->label() }}
                                </span>
                                @if($order->payment_status->isPending() && $order->payment_method->isOnline())
                                <form action="{{ route('orders.payNow', $order->order_number) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="mt-2 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg text-xs font-semibold">
                                        Pay Now
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">{{ money($order->subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-gray-900">{{ money($order->shipping_cost ?? 0) }}</span>
                            </div>
                            @if($order->discount_amount && $order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-medium">{{ money($order->discount_amount) }}</span>
                            </div>
                            @endif
                            <div class="h-px bg-gray-300 my-3"></div>
                            <div class="flex justify-between text-lg">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-brand-blue">{{ money($order->total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-clock text-brand-blue"></i>
                Order Timeline
            </h2>
            <div class="space-y-4">


                @php
                $statuses = [
                ['key' => 'placed', 'label' => 'Placed', 'date' => $order->created_at, 'always' => true],
                ['key' => 'confirmed', 'label' => 'Confirmed', 'date' => $order->confirmed_at],
                ['key' => 'processing', 'label' => 'Processing', 'date' => null],
                ['key' => 'shipped', 'label' => 'Shipped', 'date' => $order->shipped_at],
                ['key' => 'delivered', 'label' => 'Delivered', 'date' => $order->delivered_at],
                ];

                $statusOrder = [
                'pending' => 0,
                'confirmed' => 1,
                'processing' => 2,
                'shipped' => 3,
                'delivered' => 4,
                ];

                $currentIndex = $statusOrder[$order->status->value] ?? 0;
                @endphp

                @foreach($statuses as $index => $status)
                @php
                $isCompleted = ($status['always'] ?? false) || $currentIndex > $index;
                $isCurrent = $currentIndex === $index;
                $isLast = $loop->last;
                @endphp

                <div class="flex gap-4">

                    <!-- Left Timeline Indicator -->
                    <div class="flex flex-col items-center">

                        <!-- Circle -->
                        <div class="w-4 h-4 rounded-full border-4
            @if($isCompleted)
                bg-green-500 border-green-100
            @elseif($isCurrent)
                bg-blue-500 border-blue-100
            @else
                bg-gray-300 border-gray-200
            @endif
        "></div>

                        <!-- Vertical Line -->
                        @unless($isLast)
                        <div class="w-1 h-12 mt-2
            @if($currentIndex > $index)
                bg-green-300
            @else
                bg-gray-300
            @endif
        "></div>
                        @endunless
                    </div>

                    <!-- Right Content -->
                    <div>
                        <p class="font-semibold
            @if($isCompleted)
                text-gray-900
            @elseif($isCurrent)
                text-blue-600
            @else
                text-gray-400
            @endif
        ">
                            {{ $status['label'] }}
                        </p>

                        @if($status['date'])
                        <p class="text-sm text-gray-500">
                            {{ $status['date']->format('M d, Y h:i A') }}
                        </p>
                        @elseif($isCurrent)
                        <p class="text-sm text-blue-500">
                            In progress
                        </p>
                        @else
                        <p class="text-sm text-gray-400">
                            Pending
                        </p>
                        @endif
                    </div>

                </div>
                @endforeach


            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('orders.index') }}" class="flex items-center justify-center gap-2 px-8 py-3 bg-brand-blue text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 px-8 py-3 bg-white text-brand-blue border-2 border-brand-blue rounded-xl font-semibold hover:bg-blue-50 transition">
                <i class="fas fa-shopping-bag"></i>
                Continue Shopping
            </a>
        </div>

        <!-- Support Section -->
        <div class="text-center mt-8 text-sm bg-blue-50 rounded-xl p-4">
            <p class="text-gray-600 mb-2">Have questions about your order?</p>
            <a href="mailto:support@example.com" class="text-brand-blue hover:underline font-semibold">Contact our support team</a>
        </div>
    </div>
</div>
@endsection