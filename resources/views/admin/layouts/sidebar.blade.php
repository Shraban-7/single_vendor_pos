<?php
$pendingSalesCount = \App\Models\Sale::where('status', \App\Enums\SaleStatus::PENDING)->count();
$user = auth()->user();
?>
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-r border-slate-800/80 transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-2xl lg:shadow-none">

    {{-- Brand / Logo --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-800/70 bg-gradient-to-r from-indigo-500/15 via-violet-500/5 to-transparent">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group w-full">
            @if (!empty($settings['site_logo']))
                <div class="relative shrink-0">
                    <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 opacity-60 blur-[6px] group-hover:opacity-90 transition"></div>
                    <img src="{{ storage_url($settings['site_logo']) }}" alt="logo"
                         class="relative h-10 w-10 rounded-2xl object-cover ring-2 ring-white/10 shadow-lg">
                </div>
            @else
                <div class="relative shrink-0">
                    <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 opacity-60 blur-[6px] group-hover:opacity-90 transition"></div>
                    <div class="relative w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition">
                        <span class="text-lg font-extrabold text-white font-heading">{{ substr($siteName ?: 'A', 0, 1) }}</span>
                    </div>
                </div>
            @endif
            <div class="leading-tight min-w-0">
                <h1 class="text-base font-bold text-white font-heading tracking-tight truncate group-hover:text-indigo-200 transition">{{ $siteName }}</h1>
                <p class="text-[11px] text-slate-400 font-medium truncate">{{ $settings['site_tagline'] ?? 'Admin Panel' }}</p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-5 px-3 sidebar-nav scroll-smooth">
        <div class="space-y-6">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.dashboard') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                    <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                    <span class="font-heading">Dashboard</span>
                </a>
            </div>

            <div>
                <p class="px-3.5 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.12em]">Sales</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.ecommerce-sales.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.ecommerce-sales.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.ecommerce-sales.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="shopping-cart" class="w-5 h-5 {{ request()->routeIs('admin.ecommerce-sales.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Sales</span>
                        @if ($pendingSalesCount)
                            <span class="ml-auto bg-rose-500/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">{{ $pendingSalesCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.pos.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.pos.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.pos.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="shopping-bag" class="w-5 h-5 {{ request()->routeIs('admin.pos.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">POS</span>
                    </a>

                    <a href="{{ route('admin.saleReturns.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.saleReturns.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.saleReturns.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="archive-restore" class="w-5 h-5 {{ request()->routeIs('admin.saleReturns.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Returns</span>
                    </a>

                    <a href="{{ route('admin.saleExchanges.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.saleExchanges.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.saleExchanges.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="repeat" class="w-5 h-5 {{ request()->routeIs('admin.saleExchanges.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Exchanges</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3.5 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.12em]">Catalog</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.products.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.products.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="package" class="w-5 h-5 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Products</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.categories.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="tag" class="w-5 h-5 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Categories</span>
                    </a>

                    <a href="{{ route('admin.barcodes.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.barcodes.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.barcodes.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="barcode" class="w-5 h-5 {{ request()->routeIs('admin.barcodes.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Print Barcode</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3.5 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.12em]">People</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.customers.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.customers.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.customers.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="users" class="w-5 h-5 {{ request()->routeIs('admin.customers.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Customers</span>
                    </a>

                    <a href="{{ route('admin.employees.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.employees.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.employees.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="user-cog" class="w-5 h-5 {{ request()->routeIs('admin.employees.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Employees</span>
                    </a>

                    <a href="{{ route('admin.suppliers.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.suppliers.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.suppliers.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="truck" class="w-5 h-5 {{ request()->routeIs('admin.suppliers.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Suppliers</span>
                    </a>
                </div>
            </div>

            <div x-data="{ openReports: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                <p class="px-3.5 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.12em]">Finance & Reports</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.expenses.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.expenses.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.expenses.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('admin.expenses.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Expenses</span>
                    </a>

                    <a href="{{ route('admin.purchases.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.purchases.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.purchases.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="shopping-cart" class="w-5 h-5 {{ request()->routeIs('admin.purchases.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Purchases</span>
                    </a>

                    <button type="button" @click="openReports = !openReports"
                        class="sidebar-group-btn w-full group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading flex-1 text-left">Reports</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform duration-200 {{ request()->routeIs('admin.reports.*') ? 'text-white rotate-90' : 'text-slate-500' }}" :class="openReports ? 'rotate-90' : ''"></i>
                    </button>

                    <div x-show="openReports" x-collapse x-cloak class="mt-1 ml-5 pl-3 border-l border-slate-700/70 space-y-0.5">
                        <a href="{{ route('admin.reports.sales') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.sales') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Sales Report</span>
                        </a>
                        <a href="{{ route('admin.reports.purchases') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.purchases') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Purchase Report</span>
                        </a>
                        <a href="{{ route('admin.reports.profit-loss') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.profit-loss') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Profit & Loss</span>
                        </a>
                        <a href="{{ route('admin.reports.stock') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.stock') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Stock Report</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.customers') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Customer Report</span>
                        </a>
                        <a href="{{ route('admin.reports.suppliers') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.suppliers') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Supplier Report</span>
                        </a>
                        <a href="{{ route('admin.reports.expenses') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('admin.reports.expenses') ? 'text-indigo-300 font-semibold bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                            <span>Expense Report</span>
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <p class="px-3.5 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.12em]">System</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.activityLogs.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.activityLogs.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.activityLogs.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="file-clock" class="w-5 h-5 {{ request()->routeIs('admin.activityLogs.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Activity Log</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                        class="sidebar-link group relative flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'active bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-indigo-400 opacity-0 transition-opacity duration-200 {{ request()->routeIs('admin.settings.*') ? '!opacity-100' : 'group-hover:opacity-40' }}"></span>
                        <i data-lucide="settings" class="w-5 h-5 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-300' }}"></i>
                        <span class="font-heading">Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- User card --}}
    <div class="border-t border-slate-800/70 p-3 bg-gradient-to-r from-slate-800/40 to-transparent">
        <div class="flex items-center gap-3 px-2.5 py-2.5 rounded-xl hover:bg-slate-800/60 transition cursor-pointer">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/30 shrink-0">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->phone }}</p>
            </div>
            <a href="{{ route('admin.profile.edit') }}" class="text-slate-400 hover:text-indigo-300 transition" title="Account Settings">
                <i data-lucide="settings" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</aside>
