<?php
$pendingOrdersCount = \App\Models\Order::where('status', \App\Enums\OrderStatus::PENDING)->count();
$user = auth()->user();
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 -translate-x-full flex flex-col">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            @if($settings['site_logo'])
            <img src="{{ storage_url($settings['site_logo']) }}" alt="logo">
            @else
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-bolt text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">{{ $siteName }}</h1>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
            @endif
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home text-lg w-5"></i>
                <span>Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sales</p>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag text-lg w-5"></i>
                    <span>Orders</span>
                    @if($pendingOrdersCount)
                    <span class="ml-auto bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.pos.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag text-lg w-5"></i>
                    <span>POS</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Catalog</p>
                <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box text-lg w-5"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags text-lg w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-star text-lg w-5"></i>
                    <span>Reviews</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customers</p>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users text-lg w-5"></i>
                    <span>Customers</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Marketing</p>
                <a href="{{ route('admin.coupons.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt text-lg w-5"></i>
                    <span>Coupons</span>
                </a>
                <a href="{{ route('admin.banners.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="fas fa-image text-lg w-5"></i>
                    <span>Banners</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">System</p>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog text-lg w-5"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-gray-50">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $user->phone }}</p>
            </div>
        </div>
    </div>
</aside>