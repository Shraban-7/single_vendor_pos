<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
    {{-- Menu Toggle Button (All Screens) --}}
    <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-xl transition focus:outline-none">
        <i class="fas fa-bars text-xl"></i>
    </button>

    {{-- Search Bar --}}
    <div class="flex-1 max-w-2xl mx-auto hidden md:block">
        <div class="relative">
            <input type="search" placeholder="Search orders, products, customers..." class="w-full h-10 pl-10 pr-4 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 transition">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    {{-- Header Actions --}}
    <div class="flex items-center gap-3">
        {{-- Notifications --}}
        <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        {{-- View Site --}}
        <a href="{{ route('home') }}" target="_blank" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl transition">
            <i class="fas fa-external-link-alt"></i>
            <span>View Site</span>
        </a>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-xl transition">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-user-circle w-5"></i>
                    <span>My Profile</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-cog w-5"></i>
                    <span>Account Settings</span>
                </a>
                <div class="border-t border-gray-100 my-2"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition w-full text-left">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>