{{-- Sticky Header --}}
<header id="mainHeader" class="sticky top-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-100/50 transition-all duration-300">
    {{-- Top bar - Desktop only --}}
    <div class="hidden md:block bg-gradient-to-r from-gray-900 to-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between text-xs">
            <div class="flex items-center gap-6">
                <!-- <span class="flex items-center gap-2">
                    <i class="fas fa-truck text-brand-blue"></i>
                    Free Delivery on orders over ৳2000
                </span> -->
                <span class="flex items-center gap-2">
                    <i class="fas fa-phone text-brand-blue"></i>
                    Contact: {{ $settings['contact_phone'] ?? '' }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('track-order.index') }}" class="hover:text-brand-blue transition">Track Order</a>
                <span class="text-gray-500">|</span>
                <a href="#" class="hover:text-brand-blue transition">Help & Support</a>
            </div>
        </div>
    </div>

    {{-- Main Navigation --}}
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="flex items-center justify-between h-16 md:h-20">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-1 group">
                @if($settings['site_logo'])
                <img src="{{ storage_url($settings['site_logo']) }}" alt="logo" class="h-14">
                @else
                    <div class="relative">
                        <span class="text-2xl md:text-3xl font-extrabold logo-text tracking-tight">Spinner</span>
                        <span class="text-2xl md:text-3xl font-extrabold logo-accent tracking-tight">Fashion</span>
                        <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-brand-blue to-blue-400 group-hover:w-full transition-all duration-300"></div>
                    </div>
                @endif
            </a>

            {{-- Desktop Search Bar --}}
            <div class="hidden md:flex flex-1 max-w-xl mx-8">
                <button onclick="openSearch()" class="relative w-full group text-left">
                    <div class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-3 pl-12 pr-6 text-sm text-gray-400 hover:border-gray-300 hover:bg-white transition-all duration-300">
                        Search for products, brands and more...
                    </div>
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 bg-gray-200 text-gray-500 px-2 py-1 rounded-lg text-xs font-medium">
                        Ctrl+K
                    </span>
                </button>
            </div>

            {{-- Right Icons --}}
            <div class="flex items-center gap-1 md:gap-2">
                {{-- Search - Mobile --}}
                <button onclick="openSearch()" class="md:hidden icon-btn p-2.5 rounded-xl" aria-label="Search">
                    <i class="fas fa-search text-lg text-gray-600"></i>
                </button>

                {{-- Wishlist --}}
                {{--<button class="hidden md:flex icon-btn p-2.5 rounded-xl items-center gap-2 group" aria-label="Wishlist">
                    <div class="relative">
                        <i class="far fa-heart text-lg text-gray-600 group-hover:text-brand-blue transition-colors"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">2</span>
                    </div>
                    <span class="text-sm text-gray-600 group-hover:text-brand-blue transition-colors hidden lg:block">Wishlist</span>
                </button>--}}

                {{-- Cart --}}
                <button class="icon-btn p-2.5 rounded-xl flex items-center gap-2 group" aria-label="Cart" onclick="openCartDrawer()">
                    <div class="relative">
                        <i class="fas fa-shopping-bag text-lg text-gray-600 group-hover:text-brand-blue transition-colors"></i>
                        <span class="absolute -top-2 -right-2 bg-brand-blue text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold floating-badge" id="headerCartCount"></span>
                    </div>
                    <div class="hidden lg:flex flex-col items-start">
                        <span class="text-sm font-semibold text-gray-700" id="headerCartTotal"></span>
                    </div>
                </button>

                {{-- Divider --}}
                <div class="hidden md:block w-px h-8 bg-gray-200 mx-2"></div>

                {{-- Profile --}}
                @auth

                <?php
                $user = auth()->user();
                $isAdmin = $user->role == \App\Enums\UserRole::CUSTOMER ? false : true;
                ?>
                <div class="relative group" id="profileDropdown">
                    <button onclick="toggleProfileDropdown(event)" class="icon-btn p-2 rounded-xl flex items-center gap-2" aria-label="Profile">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-blue to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-lg shadow-brand-blue/25">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="hidden lg:flex flex-col items-start">
                            <span class="text-[10px] text-gray-400">Hello,</span>
                            <span class="text-sm font-semibold text-gray-700">{{ Str::limit($user->name, 12) }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden lg:block"></i>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="profileDropdownMenu" class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-2 z-[60]">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                        </div>
                        @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">Dashboard</span>
                        </a>
                        @else
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">Dashboard</span>
                        </a>
                        <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">My Profile</span>
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-box text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">Orders</span>
                        </a>
                        <a href="{{ route('customer.addresses') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">Addresses</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                            <i class="fas fa-cog text-gray-400 w-5"></i>
                            <span class="text-sm text-gray-700">Settings</span>
                        </a>
                        @endif
                        <div class="border-t border-gray-100 mt-2 pt-2">
                            <form action="{{ route('auth.logout') }}" method="POST" id="logoutForm">
                                @csrf
                                <button type="button" class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 transition w-full text-left" onclick="document.getElementById('logoutForm').submit()">
                                    <i class="fas fa-sign-out-alt text-red-500 w-5"></i>
                                    <span class="text-sm text-red-600 font-medium">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <button onclick="openAuthModal('login')" class="icon-btn p-2 rounded-xl flex items-center gap-2 group" aria-label="Profile">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-blue to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-lg shadow-brand-blue/25">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="hidden lg:flex flex-col items-start">
                        <span class="text-[10px] text-gray-400">Hello, Sign in</span>
                        <span class="text-sm font-semibold text-gray-700">Account</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 hidden lg:block"></i>
                </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- Desktop Navigation Menu --}}
    @if(request()->routeIs('home'))
    <div class="hidden md:block border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex items-center gap-8 h-12">
                {{-- All Categories Mega Menu --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-brand-blue transition-colors h-12">
                        <i class="fas fa-th-large text-brand-blue"></i>
                        All Categories
                        <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:rotate-180 transition-transform duration-200"></i>
                    </button>

                    {{-- Mega Menu Dropdown --}}
                    <div class="absolute top-full left-0 w-[800px] bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50">
                        <div class="p-6">
                            <div class="grid grid-cols-4 gap-6">
                                @php
                                $categoryIcons = [
                                'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-brand-blue'],
                                'pink' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-500'],
                                'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-500'],
                                'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-500'],
                                ];
                                $iconKeys = array_keys($categoryIcons);
                                $iconIndex = 0;
                                @endphp

                                @foreach($allMenuCategories->take(4) as $category)
                                @php
                                $iconColor = $categoryIcons[$iconKeys[$iconIndex % count($iconKeys)]];
                                $iconIndex++;
                                @endphp
                                <div>
                                    <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="flex items-center gap-2 text-sm font-bold text-brand-black mb-3 hover:text-brand-blue transition">
                                        <span class="w-8 h-8 {{ $iconColor['bg'] }} rounded-lg flex items-center justify-center">
                                            @if($category->icon)
                                            <i class="{{ $category->icon }} {{ $iconColor['text'] }}"></i>
                                            @else
                                            <i class="fas fa-tag {{ $iconColor['text'] }}"></i>
                                            @endif
                                        </span>
                                        {{ $category->name }}
                                    </a>
                                    <ul class="space-y-2">
                                        @foreach($category->children->take(7) as $child)
                                        <li><a href="{{ route('products.index') }}?category={{ $child->slug }}" class="text-sm text-gray-600 hover:text-brand-blue hover:pl-1 transition-all">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="inline-flex items-center gap-1 text-xs text-brand-blue font-medium mt-3 hover:underline">
                                        View All <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            {{-- Featured Banner --}}
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <div class="grid grid-cols-2 gap-4">
                                    <a href="{{ route('products.index') }}?collection=eid" class="relative overflow-hidden rounded-xl bg-gradient-to-r from-orange-500 to-red-500 p-5 text-white group">
                                        <div class="relative z-10">
                                            <span class="text-xs font-medium opacity-80">Limited Time</span>
                                            <h4 class="text-lg font-bold mt-1">Eid Collection 2026</h4>
                                            <p class="text-sm opacity-80 mt-1">Up to 40% Off on Premium Panjabi</p>
                                            <span class="inline-flex items-center gap-1 text-sm font-semibold mt-3 group-hover:gap-2 transition-all">
                                                Shop Now <i class="fas fa-arrow-right text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/10 text-8xl">
                                            <i class="fas fa-moon"></i>
                                        </div>
                                    </a>
                                    <a href="{{ route('products.index') }}?collection=new" class="relative overflow-hidden rounded-xl bg-gradient-to-r from-brand-blue to-blue-600 p-5 text-white group">
                                        <div class="relative z-10">
                                            <span class="text-xs font-medium opacity-80">Just Arrived</span>
                                            <h4 class="text-lg font-bold mt-1">New Arrivals</h4>
                                            <p class="text-sm opacity-80 mt-1">Fresh styles for the season</p>
                                            <span class="inline-flex items-center gap-1 text-sm font-semibold mt-3 group-hover:gap-2 transition-all">
                                                Explore <i class="fas fa-arrow-right text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/10 text-8xl">
                                            <i class="fas fa-sparkles"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Men Dropdown --}}
                @foreach($allMenuCategories->take(2) as $category)
                <div class="relative group">
                    <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-brand-blue transition-colors h-12">
                        {{ $category->name }}
                        @if($category->children->isNotEmpty())
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:rotate-180 transition-transform duration-200"></i>
                        @endif
                    </a>
                    @if($category->children->isNotEmpty())
                    <div class="absolute top-full left-0 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50 py-2">
                        @foreach($category->children as $child)
                        <a href="{{ route('products.index') }}?category={{ $child->slug }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-brand-blue/5 hover:text-brand-blue transition">{{ $child->name }}</a>
                        @endforeach
                        <div class="border-t border-gray-100 mt-2 pt-2">
                            <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="block px-4 py-2 text-sm text-brand-blue font-medium hover:bg-brand-blue/5 transition">View All {{ $category->name }} →</a>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach

                <a href="{{ route('products.index') }}?collection=new" class="text-sm font-medium text-gray-600 hover:text-brand-blue transition-colors">New Arrivals</a>

                {{-- Additional Category Links --}}
                @foreach($allMenuCategories->skip(2)->take(2) as $category)
                <div class="relative group">
                    <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-brand-blue transition-colors h-12">
                        {{ $category->name }}
                        @if($category->children->isNotEmpty())
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:rotate-180 transition-transform duration-200"></i>
                        @endif
                    </a>
                    @if($category->children->isNotEmpty())
                    <div class="absolute top-full left-0 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50 py-2">
                        @foreach($category->children as $child)
                        <a href="{{ route('products.index') }}?category={{ $child->slug }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-brand-blue/5 hover:text-brand-blue transition">{{ $child->name }}</a>
                        @endforeach
                        <div class="border-t border-gray-100 mt-2 pt-2">
                            <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="block px-4 py-2 text-sm text-brand-blue font-medium hover:bg-brand-blue/5 transition">View All {{ $category->name }} →</a>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach

                <a href="{{ route('products.index') }}?sale=1" class="text-sm font-medium text-red-500 hover:text-red-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-fire text-xs"></i>
                    Sale
                </a>
            </nav>
        </div>
    </div>

    @endif

    {{-- Search Bar (Mobile) --}}
    <div class="px-4 pb-3 md:hidden">
        <button onclick="openSearch()" class="relative w-full text-left">
            <div class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-3 pl-11 pr-4 text-sm text-gray-400">
                Search for products...
            </div>
            <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
        </button>
    </div>
</header>

<script>
    // Toggle profile dropdown on mobile
    function toggleProfileDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('profileDropdownMenu');

        if (dropdown) {
            const isVisible = !dropdown.classList.contains('opacity-0');

            if (isVisible) {
                dropdown.classList.add('opacity-0', 'invisible');
            } else {
                dropdown.classList.remove('opacity-0', 'invisible');
            }
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const menu = document.getElementById('profileDropdownMenu');

        if (dropdown && menu && !dropdown.contains(event.target)) {
            menu.classList.add('opacity-0', 'invisible');
        }
    });
</script>