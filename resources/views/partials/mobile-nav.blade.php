{{-- Mobile Bottom Navigation --}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 md:hidden z-50 safe-area-bottom">
    <div class="flex items-center justify-around py-2">

        {{-- Home --}}
        <a href="{{ url('/') }}" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="fas fa-home text-xl text-brand-blue"></i>
            <span class="text-[10px] font-medium text-brand-blue mt-0.5">Home</span>
        </a>

        {{-- Categories --}}
        <a href="{{ route('products.index') }}" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="fas fa-th-large text-xl text-gray-500"></i>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Shop</span>
        </a>

        <a href="javascript:void(0)" onclick="toggleMobileMenu(true)" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="fas fa-th-large text-xl text-gray-500"></i>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Categories</span>
        </a>

        {{-- Cart --}}
        <a href="#" class="flex flex-col items-center px-3 py-1 tap-effect relative" onclick="openCartDrawer()">
            <i class="fas fa-shopping-cart text-xl text-gray-500"></i>
            <span class="absolute -top-1 right-1 bg-brand-blue text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold">3</span>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Cart</span>
        </a>

        {{-- Wishlist --}}
        <!-- <a href="#" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="far fa-heart text-xl text-gray-500"></i>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Wishlist</span>
        </a> -->

        {{-- Account --}}
        @auth
        <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="fas fa-user text-xl text-gray-500"></i>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Account</span>
        </a>
        @else
        <button onclick="openAuthModal('login')" class="flex flex-col items-center px-3 py-1 tap-effect">
            <i class="fas fa-user text-xl text-gray-500"></i>
            <span class="text-[10px] font-medium text-gray-500 mt-0.5">Account</span>
        </button>
        @endauth

    </div>
</nav>