@extends('layouts.app')
@section('title', 'Payment Successful')
@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6 animate-pulse">
                <svg class="h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-600">Your order has been placed successfully.</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                <div>
                    <p class="text-sm text-gray-600">Order Number</p>
                    <p class="text-lg font-semibold text-gray-900">#{{ $sale->order_number }}</p>
                </div>
                <div class="bg-green-100 px-3 py-1 rounded-full">
                    <span class="text-sm font-medium text-green-800">Confirmed</span>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Payment received</p>
                        <p class="text-xs text-gray-600">We've received your payment successfully</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-envelope text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Confirmation email sent</p>
                        <p class="text-xs text-gray-600">Check your email for order details</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-truck text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Order is being processed</p>
                        <p class="text-xs text-gray-600">We'll notify you when it ships</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Redirect Message -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-brand-blue mr-3"></i>
                <p class="text-sm text-gray-700">
                    Redirecting in <span id="countdown" class="font-bold text-brand-blue">3.5</span> seconds...
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            @auth
            <a href="{{ route('admin.ecommerce-sales.index') }}" class="flex-1 bg-brand-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-brand-blue-600 transition text-center">
                <i class="fas fa-shopping-bag mr-2"></i>View Orders
            </a>
            @else
            <a href="{{ route('home') }}" class="flex-1 bg-brand-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-brand-blue-600 transition text-center">
                <i class="fas fa-home mr-2"></i>Go to Home
            </a>
            @endauth
            <a href="{{ route('products.index') }}" class="flex-1 border-2 border-brand-blue text-brand-blue py-3 px-6 rounded-lg font-medium hover:bg-brand-blue hover:text-white transition text-center">
                <i class="fas fa-shopping-cart mr-2"></i>Continue Shopping
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let timeLeft = 3.5;
    const countdownElement = document.getElementById('countdown');
    const redirectUrl = "<?php echo auth()->check() ? route('admin.ecommerce-sales.index') : route('track-order.index', ['order_number' => $sale->order_number]); ?>";

    const timer = setInterval(() => {
        timeLeft -= 0.1;
        countdownElement.textContent = Math.max(0, timeLeft).toFixed(1);

        if (timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = redirectUrl;
        }
    }, 100);
</script>
@endpush

@endsection