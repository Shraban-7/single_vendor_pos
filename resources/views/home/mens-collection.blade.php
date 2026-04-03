@if($mensProducts->isNotEmpty())
<section class="py-6 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-brand-black">Men's Collection</h2>
                <p class="text-xs md:text-sm text-brand-gray">Stylish picks for men</p>
            </div>
            <a href="{{ route('products.index', ['category' => 'men']) }}" class="text-brand-blue text-sm font-semibold flex items-center gap-1 tap-effect">
                View All
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>

        {{-- Horizontal Scroll Products --}}
        <div class="flex gap-3 md:gap-5 overflow-x-auto hide-scrollbar pb-2">
            @foreach($mensProducts as $product)
            <div class="min-w-[160px] sm:min-w-[180px] md:min-w-[220px] max-w-[160px] sm:max-w-[180px] md:max-w-[220px]">
                <x-product-card :product="$product" />
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif