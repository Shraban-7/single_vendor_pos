<?php
$pendingOrdersCount = \App\Models\Order::where('status', \App\Enums\OrderStatus::PENDING)->count();
$user = auth()->user();
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 -translate-x-full flex flex-col">
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            @if($settings['site_logo'])
            <img src="{{ storage_url($settings['site_logo']) }}" alt="logo">
            @else
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <i data-lucide="zap" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white font-heading">{{ $siteName }}</h1>
                <p class="text-xs text-slate-400">Admin Panel</p>
            </div>
            @endif
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-heading">Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sales</p>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span class="font-heading">Orders</span>
                    @if($pendingOrdersCount)
                    <span class="ml-auto bg-indigo-500/15 text-indigo-300 text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.pos.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <span class="font-heading">POS</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Catalog</p>
                <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span class="font-heading">Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                    <span class="font-heading">Categories</span>
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i data-lucide="star" class="w-5 h-5"></i>
                    <span class="font-heading">Reviews</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">People</p>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span class="font-heading">Customers</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                    <span class="font-heading">Employees</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Marketing</p>
                <a href="{{ route('admin.coupons.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i data-lucide="ticket-percent" class="w-5 h-5"></i>
                    <span class="font-heading">Coupons</span>
                </a>
                <a href="{{ route('admin.banners.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i data-lucide="image" class="w-5 h-5"></i>
                    <span class="font-heading">Banners</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">System</p>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span class="font-heading">Settings</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="border-t border-slate-800 p-4">
        <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-slate-800">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->phone }}</p>
            </div>
        </div>
    </div>
</aside>
