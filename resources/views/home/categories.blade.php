{{-- Quick Category Menu --}}
<section class="bg-gradient-to-b from-white to-gray-50 py-8 md:py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Shop by Category</h2>
            <p class="text-sm md:text-base text-gray-500">Discover our wide range of fashion collections</p>
        </div>

        <div class="grid grid-cols-3 lg:grid-cols-6 gap-6 md:gap-8">
            @foreach($allMenuCategories as $category)
            <a href="{{ route('products.index') }}?category={{ $category->slug }}" class="group">
                <div class="flex flex-col items-center text-center gap-4">
                    {{-- Image Container - Increased sizing slightly --}}
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28">
                        <div class="absolute inset-0 rounded-full overflow-hidden group-hover:scale-110 transition-all ring-2 ring-white shadow-md group-hover:shadow-xl group-hover:ring-brand-blue/50">
                            <img
                                src="{{ $category->image ? storage_url($category->image) : asset('assets/images/default.png') }}"
                                alt="{{ $category->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    <span class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-brand-blue transition-colors tracking-wide">
                        {{ $category->name }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        {{-- View All Categories Button --}}
        <div class="text-center mt-14">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-3 px-8 py-3 bg-white border-2 border-brand-blue text-brand-blue font-bold rounded-full hover:bg-brand-blue hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                <span>Explore All Collections</span>
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        </div>
    </div>
</section>