@if($bestSelling->isNotEmpty())
<section class="py-6 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-brand-black">Best Selling</h2>
                <p class="text-xs md:text-sm text-brand-gray">Customer favorites</p>
            </div>
            <a href="{{ route('products.index', ['filter' => 'best-sellers']) }}" class="text-brand-blue text-sm font-semibold flex items-center gap-1 tap-effect">
                View All
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
            @foreach($bestSelling as $product)
            <x-product-card :product="$product" badgeType="HOT" />
            @endforeach
        </div>
    </div>
</section>
@endif