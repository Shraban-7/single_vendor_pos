<?php
$banner = $banners->where('position', \App\Enums\BannerPosition::FESTIVAL)->sortBy('sort_order')->first();
?>

@if($banner)
<section class="py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="relative rounded-2xl md:rounded-3xl overflow-hidden shadow-xl">
            <img src="{{ storage_url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-auto min-h-[300px] object-cover">

            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>

            <div class="absolute inset-0 flex flex-col justify-center items-start text-left px-8 md:px-12 lg:px-16 max-w-2xl">
                <h2 class="text-white text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3 md:mb-5 tracking-tight">{{ $banner->title }}</h2>
                <p class="text-gray-100 text-base md:text-lg mb-6 md:mb-8 leading-relaxed">{{ $banner->subtitle }}</p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="bg-white text-gray-950 px-7 py-3 md:px-9 md:py-3.5 rounded-full font-bold text-sm md:text-base hover:bg-gray-100 transition duration-150 shadow-lg">{{ $banner->button_text }}</a>
                    <a href="#" class="bg-transparent border-2 border-white text-white px-7 py-3 md:px-9 md:py-3.5 rounded-full font-semibold text-sm md:text-base hover:bg-white/10 transition duration-150">View All</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{--<section class="py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="relative rounded-2xl md:rounded-3xl overflow-hidden">
            <img src="{{ storage_url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-48 sm:h-56 md:h-72 lg:h-80 object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-blue-600/60"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-6">
                <h2 class="text-white text-2xl sm:text-3xl md:text-5xl font-bold mb-2 md:mb-4">{{ $banner->title }}</h2>
                <p class="text-gray-100 text-sm md:text-lg mb-4 md:mb-6 max-w-lg">{{ $banner->subtitle }}</p>
                <div class="flex gap-3">
                    <a href="#" class="bg-white text-purple-900 px-6 py-2.5 md:px-8 md:py-3 rounded-full font-bold text-sm md:text-base hover:bg-gray-100 transition tap-effect shadow-lg">{{ $banner->button_text }}</a>
                    <a href="#" class="bg-transparent border-2 border-white text-white px-6 py-2.5 md:px-8 md:py-3 rounded-full font-semibold text-sm md:text-base hover:bg-white/10 transition tap-effect">View All</a>
                </div>
            </div>
        </div>
    </div>
</section>--}}
@endif