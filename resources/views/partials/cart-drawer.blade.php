{{-- Floating Cart Button --}}
<button onclick="openCartDrawer()" id="floatingCartBtn" class="fixed bottom-24 right-4 md:bottom-8 md:right-8 z-40 w-14 h-14 bg-brand-blue text-white rounded-full shadow-lg shadow-brand-blue/30 flex items-center justify-center hover:bg-blue-600 transition-all tap-effect group">
    <i class="fas fa-shopping-bag text-xl group-hover:scale-110 transition-transform"></i>
    {{-- Cart Count Badge --}}
    <span id="cartCountBadge" class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white">3</span>
</button>

{{-- Cart Drawer Overlay --}}
<div id="cartOverlay" onclick="closeCartDrawer()" class="fixed inset-0 bg-black/50 z-50 opacity-0 invisible transition-opacity duration-300"></div>

{{-- Cart Drawer --}}
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col">

    {{-- Cart Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-blue/10 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-brand-blue"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-brand-black">Shopping Cart</h2>
                <p class="text-xs text-gray-500"><span id="cartItemCount">3</span> items</p>
            </div>
        </div>
        <button onclick="closeCartDrawer()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition tap-effect">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
    </div>

    {{-- Free Shipping Progress --}}
    <div class="hidden px-5 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-600" id="shippingRemaining">Add <span class="font-semibold text-green-600">৳1,500</span> more for free shipping!</span>
            <i class="fas fa-truck text-green-500 text-sm"></i>
        </div>
        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div id="shippingProgressBar" class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
    </div>

    {{-- Cart Items --}}
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4" id="cartItemsContainer">
        {{-- Cart items will be loaded dynamically here --}}
    </div>

    {{-- Empty Cart State (Hidden by default) --}}
    <div id="emptyCartState" class="hidden flex-1 flex flex-col items-center justify-center px-5 py-10">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-shopping-bag text-4xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Your cart is empty</h3>
        <p class="text-sm text-gray-500 text-center mb-6">Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('products.index') }}" onclick="closeCartDrawer()" class="bg-brand-blue text-white px-6 py-3 rounded-xl font-semibold text-sm hover:bg-blue-600 transition tap-effect">
            Start Shopping
        </a>
    </div>

    {{-- Coupon Code --}}
    <div class="px-5 py-3 border-t border-gray-100" id="couponSection">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <input type="text" placeholder="Enter coupon code" class="w-full h-11 px-4 pr-10 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-blue transition">
                <i class="fas fa-tag absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
            <button class="h-11 px-5 bg-gray-900 text-white rounded-xl font-semibold text-sm hover:bg-gray-800 transition tap-effect">
                Apply
            </button>
        </div>
    </div>

    {{-- Cart Footer --}}
    <div class="px-5 py-4 border-t border-gray-100 bg-white" id="cartFooter">
        {{-- Price Summary --}}
        <div class="space-y-2 mb-4">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-medium text-gray-900" id="cartSubtotal">৳0</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Shipping</span>
                <span class="font-medium text-gray-900" id="cartShipping">৳60</span>
            </div>
            <div class="flex items-center justify-between text-sm text-green-600">
                <span>Discount</span>
                <span class="font-medium" id="cartDiscount">-৳0</span>
            </div>
            <div class="h-px bg-gray-200 my-2"></div>
            <div class="flex items-center justify-between">
                <span class="text-base font-semibold text-gray-900">Total</span>
                <span class="text-xl font-bold text-brand-blue" id="cartTotal">৳0</span>
            </div>
        </div>

        {{-- Checkout Button --}}
        <a href="{{ route('checkout.index') }}" class="w-full bg-brand-blue text-white py-4 rounded-xl font-bold text-center hover:bg-blue-600 transition tap-effect shadow-lg shadow-brand-blue/20 flex items-center justify-center gap-2 mb-3">
            <i class="fas fa-lock text-sm"></i>
            Proceed to Checkout
        </a>

        {{-- Continue Shopping --}}
        <button onclick="closeCartDrawer()" class="w-full text-center text-sm text-gray-500 hover:text-brand-blue transition py-2">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Continue Shopping
        </button>

        {{-- Payment Methods --}}
        <div class="flex items-center justify-center gap-3 mt-4 pt-4 border-t border-gray-100">
            <img src="{{ asset('assets/images/bkash.png') }}" alt="bKash" class="h-6 opacity-60 hover:opacity-100 transition">
            <img src="{{ asset('assets/images/nagad.png') }}" alt="Nagad" class="h-6 opacity-60 hover:opacity-100 transition">
            <img src="{{ asset('assets/images/visa.png') }}" alt="Visa" class="h-4 opacity-60 hover:opacity-100 transition">
            <img src="{{ asset('assets/images/mastercard.png') }}" alt="Mastercard" class="h-6 opacity-60 hover:opacity-100 transition">
        </div>
    </div>

</div>