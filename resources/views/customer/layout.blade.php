@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-6 md:py-12">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Dashboard Header -->
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">My Account</h1>
            <p class="text-gray-600 mt-1">Welcome back, {{auth()->user()->name }}!</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Navigation -->
            <aside class="lg:w-64 flex-shrink-0">
                <!-- Mobile Dashboard Menu Toggle -->
                <button onclick="toggleMobileMenu()"
                    class="lg:hidden w-full bg-white rounded-lg shadow-md p-4 mb-4 flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="flex items-center gap-2 font-semibold text-gray-700">
                        <i class="fas fa-bars"></i>
                        Dashboard Menu
                    </span>
                    <i class="fas fa-chevron-down" id="menu-chevron"></i>
                </button>

                <!-- Navigation Menu -->
                <nav id="dashboard-nav"
                    class="hidden lg:block bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 bg-brand-blue text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-blue font-bold text-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-semibold truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-brand-blue-100 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <ul class="py-2">
                        <li>
                            <a href="{{ route('customer.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('customer.dashboard') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-home w-5"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.index') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('orders.*') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-shopping-bag w-5"></i>
                                <span class="font-medium">My Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.wishlist') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('customer.wishlist') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-heart w-5"></i>
                                <span class="font-medium">Wishlist</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.addresses') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('customer.addresses') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span class="font-medium">Addresses</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.profile') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('customer.profile') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-user w-5"></i>
                                <span class="font-medium">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.reviews') }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-brand-blue-50 transition {{ request()->routeIs('customer.reviews') ? 'bg-brand-blue-50 text-brand-blue border-r-4 border-brand-blue' : 'text-gray-700' }}">
                                <i class="fas fa-star w-5"></i>
                                <span class="font-medium">My Reviews</span>
                            </a>
                        </li>
                        <li class="border-t border-gray-200 mt-2 pt-2">
                            <form action="{{ route('auth.logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 transition">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span class="font-medium">Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 flex items-start gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <div>
                        <p class="font-semibold">Error!</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                @yield('dashboard-content')
            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleMobileMenu() {
        const nav = document.getElementById('dashboard-nav');
        const chevron = document.getElementById('menu-chevron');

        if (nav.classList.contains('hidden')) {
            nav.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            nav.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endpush
@endsection