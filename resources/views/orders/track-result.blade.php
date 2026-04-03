@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Back Button & Header -->
        <div class="mb-8">
            <a href="{{ route('track-order.index') }}" class="inline-flex items-center gap-2 text-brand-blue hover:text-blue-700 font-medium mb-4">
                <i class="fas fa-arrow-left"></i> Track Another Order
            </a>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Order Number</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">#{{ $order->order_number }}</h1>
                </div>
                <div class="flex items-center gap-2 bg-{{ $order->status->color() }}-100 px-4 py-3 rounded-full">
                    <div class="w-3 h-3 rounded-full animate-pulse bg-{{ $order->status->color() }}-600"></div>
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
                    <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Estimated Delivery</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($order->status->label() === 'Delivered')
                        {{ $order->delivered_at ? $order->delivered_at->format('F d, Y') : 'Delivered' }}
                        @else
                        {{ $order->created_at->addDays(5)->format('F d, Y') }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="py-6 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <i class="fas fa-map-marker-alt text-brand-blue text-lg"></i>
                    Delivery Address
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Recipient Name</p>
                            <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Phone Number</p>
                            <p class="font-medium text-gray-900">{{ $order->shipping_phone }}</p>
                        </div>
                        @if($order->shipping_email)
                        <div>
                            <p class="text-gray-500 mb-1">Email</p>
                            <p class="font-medium text-gray-900">{{ $order->shipping_email }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Address</p>
                            <p class="font-medium text-gray-900">{{ $order->shipping_address }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-gray-900">{{ $order->shipping_city }},</span>
                            <span class="font-medium text-gray-900">{{ $order->shipping_district }}</span>
                        </div>
                        @if($order->shipping_postal_code)
                        <div>
                            <p class="text-gray-500 mb-1">Postal Code</p>
                            <p class="font-medium text-gray-900">{{ $order->shipping_postal_code }}</p>
                        </div>
                        @endif
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
                            @if($item->product && $item->product->thumbnail)
                            <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-2xl"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $item->product_name }}</h4>
                            <div class="space-y-1 text-xs text-gray-600 mb-3">
                                @if($item->size)
                                <p><span class="font-medium">Size:</span> {{ $item->size }}</p>
                                @endif
                                @if($item->color)
                                <p><span class="font-medium">Color:</span> {{ $item->color }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Qty: <strong>{{ $item->quantity }}</strong></span>
                                <span class="text-lg font-bold text-gray-900">৳{{ number_format($item->subtotal, 2) }}</span>
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
                            </div>
                            @if($order->tracking_number)
                            <div>
                                <p class="text-gray-500 mb-1">Tracking Number</p>
                                <p class="font-mono font-medium text-gray-900">{{ $order->tracking_number }}</p>
                            </div>
                            @endif
                            @if($order->courier)
                            <div>
                                <p class="text-gray-500 mb-1">Courier</p>
                                <p class="font-medium text-gray-900">{{ $order->courier }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-gray-900">৳{{ number_format($order->shipping_cost ?? 0, 2) }}</span>
                            </div>
                            @if($order->discount_amount && $order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-medium">-৳{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($order->tax_amount && $order->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium text-gray-900">৳{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="h-px bg-gray-300 my-3"></div>
                            <div class="flex justify-between text-lg">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-brand-blue">৳{{ number_format($order->total, 2) }}</span>
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
                <!-- Order Placed -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full border-4 border-green-100"></div>
                        <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                    </div>
                    <div class="pb-8">
                        <p class="font-semibold text-gray-900">Order Placed</p>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>

                @php
                $statusSteps = [
                'confirmed' => ['label' => 'Order Confirmed', 'date' => $order->confirmed_at, 'icon' => 'check-circle'],
                'processing' => ['label' => 'Processing', 'date' => null, 'icon' => 'cog'],
                'shipped' => ['label' => 'Shipped', 'date' => $order->shipped_at, 'icon' => 'shipping-fast'],
                'delivered' => ['label' => 'Delivered', 'date' => $order->delivered_at, 'icon' => 'box-check']
                ];

                $currentStatusValue = $order->status->value;
                $statusOrder = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4, 'cancelled' => -1];
                $currentIndex = $statusOrder[$currentStatusValue] ?? 0;
                @endphp

                @foreach($statusSteps as $key => $step)
                @php
                $stepIndex = $statusOrder[$key] ?? 0;
                $isCompleted = $currentIndex >= $stepIndex;
                $isCurrent = $currentStatusValue === $key;
                $isLast = $loop->last;
                @endphp
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full border-4 {{ $isCompleted ? 'bg-green-500 border-green-100' : ($isCurrent ? 'bg-yellow-500 border-yellow-100' : 'bg-gray-300 border-gray-200') }}"></div>
                        @if(!$isLast)
                        <div class="w-1 h-12 {{ $isCompleted ? 'bg-green-300' : 'bg-gray-300' }} mt-2"></div>
                        @endif
                    </div>
                    <div class="{{ !$isLast ? 'pb-8' : '' }}">
                        <p class="font-semibold {{ $isCompleted || $isCurrent ? 'text-gray-900' : 'text-gray-500' }}">
                            {{ $step['label'] }}
                            @if($isCurrent)
                            <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">In Progress</span>
                            @endif
                        </p>
                        @if($isCompleted && $step['date'])
                        <p class="text-sm text-gray-500">{{ $step['date']->format('M d, Y \a\t g:i A') }}</p>
                        @elseif($isCompleted)
                        <p class="text-sm text-gray-500">Completed</p>
                        @else
                        <p class="text-sm text-gray-400">Pending</p>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Cancelled Status (if applicable) -->
                @if($order->status->value === 'cancelled')
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 bg-red-500 rounded-full border-4 border-red-100"></div>
                    </div>
                    <div>
                        <p class="font-semibold text-red-600">Order Cancelled</p>
                        @if($order->cancelled_at)
                        <p class="text-sm text-gray-500">{{ $order->cancelled_at->format('M d, Y \a\t g:i A') }}</p>
                        @endif
                        @if($order->cancellation_reason)
                        <p class="text-sm text-gray-600 mt-1">Reason: {{ $order->cancellation_reason }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Notes (if any) -->
        @if($order->notes)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                <i class="fas fa-sticky-note text-yellow-600"></i>
                Order Notes
            </h3>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('track-order.index') }}" class="flex items-center justify-center gap-2 px-8 py-3 bg-brand-blue text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-search"></i>
                Track Another Order
            </a>
            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 px-8 py-3 bg-white text-brand-blue border-2 border-brand-blue rounded-xl font-semibold hover:bg-blue-50 transition">
                <i class="fas fa-shopping-bag"></i>
                Continue Shopping
            </a>
        </div>

        <!-- Support Section -->
        <div class="text-center mt-8 text-sm bg-blue-50 rounded-xl p-4">
            <p class="text-gray-600 mb-2">Have questions about your order?</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="mailto:support@spinnerfashion.com" class="text-brand-blue hover:underline font-semibold">
                    <i class="fas fa-envelope mr-1"></i>
                    Email Support
                </a>
                <span class="hidden sm:inline text-gray-400">|</span>
                <a href="tel:+8801712345678" class="text-brand-blue hover:underline font-semibold">
                    <i class="fas fa-phone mr-1"></i>
                    Call Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection