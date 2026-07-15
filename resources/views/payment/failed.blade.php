@extends('layouts.app')
@section('title', 'Payment Failed')
@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full">
        <!-- Error Icon -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                <svg class="h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Failed</h1>
            <p class="text-gray-600">We couldn't process your payment.</p>
        </div>

        <!-- Error Details Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-red-900">Transaction Declined</p>
                        <p class="text-xs text-red-700 mt-1">Your payment could not be processed at this time</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-medium text-gray-900 mb-2">Common reasons for payment failure:</p>
                <div class="flex items-start">
                    <i class="fas fa-circle text-red-600 text-xs mt-1.5 mr-3"></i>
                    <p class="text-sm text-gray-700">Insufficient funds in account</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-circle text-red-600 text-xs mt-1.5 mr-3"></i>
                    <p class="text-sm text-gray-700">Incorrect payment details</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-circle text-red-600 text-xs mt-1.5 mr-3"></i>
                    <p class="text-sm text-gray-700">Card limit exceeded</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-circle text-red-600 text-xs mt-1.5 mr-3"></i>
                    <p class="text-sm text-gray-700">Bank security check failed</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-circle text-red-600 text-xs mt-1.5 mr-3"></i>
                    <p class="text-sm text-gray-700">Network or connection issues</p>
                </div>
            </div>
        </div>

        <!-- What to do next -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm font-medium text-gray-900 mb-3">
                <i class="fas fa-question-circle text-brand-blue mr-2"></i>What should you do?
            </p>
            <div class="space-y-2 text-xs text-gray-700">
                <p>✓ Verify your payment information</p>
                <p>✓ Check your account balance</p>
                <p>✓ Contact your bank if needed</p>
                <p>✓ Try a different payment method</p>
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

        <!-- Support Contact -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Need help? <a href="#" class="text-brand-blue font-medium hover:underline">Contact Support</a>
            </p>
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