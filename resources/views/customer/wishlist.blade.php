@extends('customer.layout')
@section('title', 'Wishlist')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">My Wishlist</h2>
        <p class="text-gray-600 mt-1">{{ $wishlists->count() }} items saved for later</p>
    </div>

    @if($wishlists->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach($wishlists as $wishlist)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
            <!-- Product Image -->
            <div class="relative aspect-square overflow-hidden bg-gray-100">
                @if($wishlist->product->images->count() > 0)
                <img src="{{ asset('storage/' . $wishlist->product->images->first()->image_path) }}"
                    alt="{{ $wishlist->product->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                @else
                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
                @endif

                <!-- Remove from Wishlist -->
                <button onclick="removeFromWishlist({{ $wishlist->id }})"
                    class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md hover:bg-red-50 transition">
                    <i class="fas fa-times text-red-600"></i>
                </button>

                <!-- Stock Badge -->
                @if($wishlist->product->stock_quantity <= 0)
                    <div class="absolute top-2 left-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold">
                    Out of Stock
            </div>
            @elseif($wishlist->product->stock_quantity <= 5)
                <div class="absolute top-2 left-2 bg-orange-600 text-white px-2 py-1 rounded text-xs font-semibold">
                Low Stock
        </div>
        @endif
    </div>

    <!-- Product Details -->
    <div class="p-4">
        <a href="{{ route('products.show', $wishlist->product->slug) }}"
            class="block hover:text-brand-blue transition">
            <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2">
                {{ $wishlist->product->name }}
            </h3>
        </a>

        <div class="flex items-center gap-2 mb-3">
            @if($wishlist->product->discount_price)
            <span class="text-lg font-bold text-brand-blue">
                ৳{{ number_format($wishlist->product->discount_price, 2) }}
            </span>
            <span class="text-sm text-gray-400 line-through">
                ৳{{ number_format($wishlist->product->price, 2) }}
            </span>
            @else
            <span class="text-lg font-bold text-gray-800">
                ৳{{ number_format($wishlist->product->price, 2) }}
            </span>
            @endif
        </div>

        <!-- Add to Cart Button -->
        @if($wishlist->product->stock_quantity > 0)
        <button onclick="addToCart({{ $wishlist->product->id }})"
            class="w-full bg-brand-blue text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-blue-700 transition text-sm">
            <i class="fas fa-shopping-cart mr-1"></i>
            Add to Cart
        </button>
        @else
        <button disabled
            class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-medium cursor-not-allowed text-sm">
            Out of Stock
        </button>
        @endif
    </div>
</div>
@endforeach
</div>
@else
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
        <i class="fas fa-heart text-gray-400 text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Your Wishlist is Empty</h3>
    <p class="text-gray-600 mb-6">Save your favorite items to buy them later.</p>
    <a href="{{ route('products.index') }}"
        class="inline-block bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition">
        <i class="fas fa-shopping-bag mr-2"></i>
        Browse Products
    </a>
</div>
@endif
</div>

@push('scripts')
<script>
    function removeFromWishlist(wishlistId) {
        if (!confirm('Remove this item from your wishlist?')) {
            return;
        }

        fetch(`/wishlist/${wishlistId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function addToCart(productId) {
        // This would integrate with your existing cart functionality
        console.log('Add to cart:', productId);
        alert('Add to cart functionality - integrate with your existing cart system');
    }
</script>
@endpush
@endsection