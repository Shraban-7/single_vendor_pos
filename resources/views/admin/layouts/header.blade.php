<header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
    {{-- Menu Toggle Button (All Screens) --}}
    <button id="sidebarToggle" class="text-slate-500 hover:text-slate-700 hover:bg-slate-100 p-2 rounded-xl transition focus:outline-none">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Search Bar --}}
    <div class="flex-1 max-w-2xl mx-auto hidden md:block">
        <div class="relative">
            <input type="search" placeholder="Search orders, products, customers..." class="w-full h-10 pl-10 pr-4 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 transition">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </div>
    </div>

    {{-- Header Actions --}}
    <div class="flex items-center gap-3">
        {{-- Notifications --}}
        <button class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
        </button>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-slate-100 rounded-xl transition">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-500"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg py-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="circle-user" class="w-5 h-5"></i>
                    <span>My Profile</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Account Settings</span>
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition w-full text-left">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
