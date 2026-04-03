@if($womensProducts->isNotEmpty())
<section class="py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-brand-black">Ladies Collection</h2>
                <p class="text-xs md:text-sm text-brand-gray">Elegant styles for women</p>
            </div>
            <a href="{{ route('products.index', ['category' => 'women']) }}" class="text-brand-blue text-sm font-semibold flex items-center gap-1 tap-effect">
                View All
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
            @foreach($womensProducts as $product)
            <div class="max-w-[220px] mx-auto w-full">
                <x-product-card :product="$product" />
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif