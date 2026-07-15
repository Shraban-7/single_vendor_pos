@extends('layouts.app')
@section('title', 'Payment Cancelled')
@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full">
        <!-- Warning Icon -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-100 mb-6">
                <svg class="h-16 w-16 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Cancelled</h1>
            <p class="text-gray-600">You have cancelled the payment process.</p>
        </div>

        <!-- Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="space-y-4">
                <div class="flex items-start">
                    <i class="fas fa-times-circle text-yellow-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Payment was not processed</p>
                        <p class="text-xs text-gray-600">No charges were made to your account</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shopping-cart text-yellow-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Your cart is still saved</p>
                        <p class="text-xs text-gray-600">You can complete your purchase anytime</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-headset text-yellow-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Need help?</p>
                        <p class="text-xs text-gray-600">Contact our support team for assistance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700 mb-2">
                <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                <span class="font-medium">Why did this happen?</span>
            </p>
            <ul class="text-xs text-gray-600 space-y-1 ml-6">
                <li>• You clicked the cancel or back button</li>
                <li>• Payment window was closed</li>
                <li>• Session timeout</li>
            </ul>
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
            <a href="{{ route('checkout.index') }}" class="flex-1 bg-brand-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-brand-blue-600 transition text-center">
                <i class="fas fa-redo mr-2"></i>Try Again
            </a>
            @auth
            <a href="{{ route('admin.ecommerce-sales.index') }}" class="flex-1 border-2 border-brand-blue text-brand-blue py-3 px-6 rounded-lg font-medium hover:bg-brand-blue hover:text-white transition text-center">
                <i class="fas fa-shopping-bag mr-2"></i>View Orders
            </a>
            @else
            <a href="{{ route('home') }}" class="flex-1 border-2 border-brand-blue text-brand-blue py-3 px-6 rounded-lg font-medium hover:bg-brand-blue hover:text-white transition text-center">
                <i class="fas fa-home mr-2"></i>Go to Home
            </a>
            @endauth
        </div>
    </div>
</div>

@push('scripts')
<script>
    let timeLeft = 3.5;
    const countdownElement = document.getElementById('countdown');
    const redirectUrl = "<?php echo auth()->check() ? route('admin.ecommerce-sales.index') : route('home'); ?>";

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