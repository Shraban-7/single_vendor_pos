@props(['product', 'showBadge' => true, 'badgeType' => 'auto'])

<?php
$badge = null;
$badgeColor = 'green';

if ($showBadge && $badgeType === 'auto') {
    if ($product->is_new_arrival) {
        $badge = 'NEW';
        $badgeColor = 'green';
    } elseif ($product->is_best_seller) {
        $badge = 'HOT';
        $badgeColor = 'red';
    } elseif ($product->is_on_sale) {
        $badge = 'SALE';
        $badgeColor = 'orange';
    } elseif ($product->is_featured) {
        $badge = 'FEATURED';
        $badgeColor = 'blue';
    }
} elseif ($showBadge && $badgeType !== 'auto') {
    $badge = $badgeType;
    $badgeColor = 'green';
}

$discountPercent = 0;
if ($product->compare_price && $product->compare_price > $product->price) {
    $discountPercent = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
}
?>

<div class="product-card group bg-white rounded-lg overflow-hidden border border-gray-200 
            hover:border-blue-400 
            transition-colors duration-300 flex flex-col h-full">
    <div class="relative overflow-hidden bg-gray-50">
        <a href="{{ route('products.show', $product->slug) }}" class="block">
            <img
                src="{{ $product->thumbnail }}"
                alt="{{ $product->name }}"
                class="w-full aspect-[3/4] object-cover object-center transform group-hover:scale-105 transition-transform duration-500 will-change-transform"
                loading="lazy">
        </a>

        {{-- Badge --}}
        @if($badge)
        <span class="absolute top-2.5 left-2.5 bg-{{ $badgeColor }}-500 text-white text-[10px] font-medium px-2 py-0.5 rounded shadow-sm">
            {{ $badge }}
        </span>
        @endif

        {{-- Top Right Actions --}}
        <div class="absolute top-2.5 right-2.5 flex flex-col gap-2">
            {{-- Wishlist Button --}}
            {{--<button
                class="wishlist-btn w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm hover:bg-red-50 transition-colors tap-effect"
                data-product-id="{{ $product->id }}"
                aria-label="Add to wishlist">
                <i class="far fa-heart text-gray-600 text-sm"></i>
            </button>--}}

            {{-- Quick View Button --}}
            <!-- <button
                class="quick-view-btn w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm hover:bg-gray-100 transition-colors tap-effect"
                aria-label="Quick view" data-product-id="{{ $product->id }}">
                <i class="far fa-eye text-gray-600 text-sm"></i>
            </button> -->
        </div>
    </div>

    <div class="p-3 flex flex-col flex-1">
        {{-- Product Name --}}
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="text-sm font-medium text-gray-800 mb-1.5 line-clamp-2 hover:text-brand-blue transition-colors leading-snug">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Rating --}}
        @if($product->review_count > 0)
        <div class="flex items-center gap-1 mb-2">
            <div class="flex text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <=floor($product->average_rating))
                    <i class="fas fa-star text-[10px]"></i>
                    @elseif($i - 0.5 <= $product->average_rating)
                        <i class="fas fa-star-half-alt text-[10px]"></i>
                        @else
                        <i class="far fa-star text-[10px]"></i>
                        @endif
                        @endfor
            </div>
            <span class="text-[10px] text-gray-500">({{ $product->review_count }})</span>
        </div>
        @endif

        {{-- Price --}}
        <div class="flex items-center gap-1.5 mb-2.5 flex-wrap">
            <span class="text-gray-900 font-semibold text-base">৳{{ number_format($product->price, 0) }}</span>
            @if($product->compare_price && $product->compare_price > $product->price)
            <span class="text-gray-400 text-xs line-through">৳{{ number_format($product->compare_price, 0) }}</span>
            @if($discountPercent > 0)
            <span class="text-green-600 text-[10px] font-medium">-{{ $discountPercent }}%</span>
            @endif
            @endif
        </div>

        {{-- Add to Cart Button --}}
        <div class="flex gap-1.5 mt-auto">
            <button
                class="add-to-cart-btn flex-1 bg-brand-blue text-white py-1.5 rounded-lg font-normal text-sm hover:bg-blue-600 transition-colors flex items-center justify-center gap-1.5 tap-effect min-w-0"
                onclick="handleProductCardAddToCart({{ $product->id }}, {{ $product->variants->count() }})"
                data-product-id="{{ $product->id }}"
                aria-label="Add to cart">
                <i class="fas fa-shopping-cart text-xs flex-shrink-0"></i>
                <span class="hidden xs:inline sm:inline">Add to Cart</span>
                <span class="inline xs:hidden sm:hidden">Add</span>
            </button>
            <button
                class="quick-view-btn w-10 flex-shrink-0 bg-gray-100 text-gray-700 py-1.5 rounded-lg hover:bg-gray-200 transition-colors tap-effect"
                data-product-id="{{ $product->id }}"
                aria-label="Quick view">
                <i class="far fa-eye text-xs"></i>
            </button>
        </div>
    </div>
</div>

@isset($oldProductCard)
<div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}">
            <img
                src="{{ $product->image ? storage_url($product->image) : asset('assets/images/default.png') }}"
                alt="{{ $product->name }}"
                class="w-full h-40 sm:h-48 md:h-56 object-cover">
        </a>

        @if($badge)
        <span class="absolute top-2 left-2 bg-{{ $badgeColor }}-500 text-white text-[10px] md:text-xs font-semibold px-2 py-1 rounded-full">
            {{ $badge }}
        </span>
        @endif

        {{-- Wishlist Button --}}
        {{--<button
            class="wishlist-btn absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow hover:bg-white transition tap-effect"
            data-product-id="{{ $product->id }}">
            <i class="far fa-heart text-gray-600"></i>
        </button>--}}
    </div>

    <div class="p-3 md:p-4">
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="text-sm md:text-base font-medium text-brand-black mb-1 line-clamp-2 hover:text-brand-blue transition">
                {{ $product->name }}
            </h3>
        </a>

        @if($product->review_count > 0)
        <div class="flex items-center gap-1 mb-2">
            <div class="flex text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <=floor($product->average_rating))
                    <i class="fas fa-star text-xs md:text-sm"></i>
                    @elseif($i - 0.5 <= $product->average_rating)
                        <i class="fas fa-star-half-alt text-xs md:text-sm"></i>
                        @else
                        <i class="far fa-star text-xs md:text-sm"></i>
                        @endif
                        @endfor
            </div>
            <span class="text-[10px] md:text-xs text-gray-500">({{ $product->review_count }})</span>
        </div>
        @endif

        <div class="flex items-center gap-2 mb-3">
            <span class="text-brand-blue font-bold text-base md:text-lg">৳{{ number_format($product->price, 0) }}</span>
            @if($product->compare_price && $product->compare_price > $product->price)
            <span class="text-gray-400 text-xs line-through">৳{{ number_format($product->compare_price, 0) }}</span>
            @if($discountPercent > 0)
            <span class="text-green-600 text-[10px] md:text-xs font-semibold">-{{ $discountPercent }}%</span>
            @endif
            @endif
        </div>

        <button
            class="add-to-cart-btn w-full bg-brand-blue text-white py-2 md:py-2.5 rounded-xl font-semibold text-xs md:text-sm hover:bg-blue-600 transition tap-effect"
            onclick="handleProductCardAddToCart({{ $product->id }}, {{ $product->variants->count() }})"
            data-product-id="{{ $product->id }}">
            <i class="fas fa-shopping-bag mr-1"></i>
            Add to Cart
        </button>
    </div>
</div>
@endisset