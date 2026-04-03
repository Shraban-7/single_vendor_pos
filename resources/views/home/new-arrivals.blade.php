{{-- New Arrivals Section --}}
@if($newArrivals->isNotEmpty())
<section class="py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        {{-- Section Header --}}
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-brand-black">New Arrivals</h2>
                <p class="text-xs md:text-sm text-brand-gray">Fresh styles just dropped</p>
            </div>
            <a href="{{ route('products.index', ['filter' => 'new-arrivals']) }}" class="text-brand-blue text-sm font-semibold flex items-center gap-1 tap-effect">
                View All
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>

        {{-- Products Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
            @foreach($newArrivals as $product)
            <x-product-card :product="$product" badgeType="NEW" />
            @endforeach
        </div>
    </div>
</section>
@endif