@extends('layouts.app')
@section('title', '404 Not Found')
@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
            <div class="relative inline-block">
                <!-- Large 404 Text -->
                <h1 class="text-9xl md:text-[12rem] font-bold text-brand-blue opacity-20 select-none">404</h1>

                <!-- Icon Overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="bg-white rounded-full p-6 shadow-lg">
                        <svg class="h-16 w-16 md:h-20 md:w-20 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Oops! Page Not Found</h2>
            <p class="text-lg text-gray-600 mb-2">The page you're looking for doesn't exist or has been moved.</p>
            <p class="text-sm text-gray-500">It seems you've wandered off the fashion runway!</p>
        </div>

        <!-- Suggestions Card -->
        <div class="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8">
            <p class="text-sm font-medium text-gray-900 mb-4">Here's what you can do:</p>
            <div class="grid md:grid-cols-2 gap-4 text-left">
                <div class="flex items-start">
                    <i class="fas fa-home text-brand-blue mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Go to Homepage</p>
                        <p class="text-xs text-gray-600">Start fresh from the beginning</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-search text-brand-blue mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Search Products</p>
                        <p class="text-xs text-gray-600">Find what you're looking for</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shopping-bag text-brand-blue mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Browse Collections</p>
                        <p class="text-xs text-gray-600">Explore our latest products</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-headset text-brand-blue mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Contact Support</p>
                        <p class="text-xs text-gray-600">We're here to help you</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center bg-brand-blue text-white py-3 px-8 rounded-lg font-medium hover:bg-brand-blue-600 transition shadow-md hover:shadow-lg">
                <i class="fas fa-home mr-2"></i>Back to Home
            </a>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center border-2 border-brand-blue text-brand-blue py-3 px-8 rounded-lg font-medium hover:bg-brand-blue hover:text-white transition">
                <i class="fas fa-shopping-bag mr-2"></i>Shop Now
            </a>
        </div>

        <!-- Popular Categories -->
        <div class="border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-600 mb-4">Or explore popular categories:</p>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="{{ route('products.index') }}?category=men" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-brand-blue hover:text-white transition">
                    <i class="fas fa-male mr-1"></i>Men's Fashion
                </a>
                <a href="{{ route('products.index') }}?category=women" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-brand-blue hover:text-white transition">
                    <i class="fas fa-female mr-1"></i>Women's Fashion
                </a>
                <a href="{{ route('products.index') }}?category=new" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-brand-blue hover:text-white transition">
                    <i class="fas fa-star mr-1"></i>New Arrivals
                </a>
                <a href="{{ route('products.index') }}?sale=1" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-brand-blue hover:text-white transition">
                    <i class="fas fa-tag mr-1"></i>Sale Items
                </a>
            </div>
        </div>
    </div>
</div>

@endsection