<?php
$sliders = $banners->where('position', \App\Enums\BannerPosition::HERO)->sortBy('sort_order');
$promos = $banners->where('position', \App\Enums\BannerPosition::PROMOTIONAL)->sortBy('sort_order');
?>

<section class="py-6 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Main Container: Stacked on mobile, 500px fixed height on desktop --}}
        <div class="flex flex-col lg:flex-row gap-4 lg:h-[500px]">

            {{-- Left Side: Main Slider (70%) --}}
            <div class="w-full lg:w-[70%] relative group overflow-hidden rounded-2xl shadow-sm bg-gray-200 aspect-[16/9] lg:aspect-auto h-full">
                <div id="hero-slider" class="h-full w-full relative">
                    @foreach ($sliders as $index => $banner)
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                        <a href="{{ $banner->link ?? '#' }}" class="block h-full">
                            <img src="{{ storage_url($banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                        </a>
                    </div>
                    @endforeach
                </div>

                {{-- Navigation --}}
                <button id="prevBtn" class="absolute z-20 left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-chevron-left text-gray-800"></i>
                </button>
                <button id="nextBtn" class="absolute z-20 right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <i class="fas fa-chevron-right text-gray-800"></i>
                </button>

                {{-- Progress Indicators --}}
                <div class="absolute z-20 bottom-4 left-6 flex gap-2">
                    @foreach ($sliders as $index => $banner)
                    <div class="w-10 h-1 rounded-full bg-white/30 overflow-hidden">
                        <div class="progress-fill h-full bg-white w-0"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Side: Two Promo Banners --}}
            {{-- Mobile: Side-by-side (flex-row) | Desktop: Stacked (lg:flex-col) --}}
            <div class="w-full lg:w-[30%] flex flex-row lg:flex-col gap-4 h-[150px] sm:h-[200px] lg:h-full">
                @foreach ($promos->take(2) as $promo)
                <div class="relative flex-1 overflow-hidden rounded-2xl bg-gray-200 group">
                    <a href="{{ $promo->link ?? '#' }}" class="block w-full h-full">
                        <img src="{{ storage_url($promo->image) }}"
                            alt="{{ $promo->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        {{-- Subtle dark overlay to make white text on images pop if you add any later --}}
                        <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors"></div>
                    </a>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.hero-slide');
        const fills = document.querySelectorAll('.progress-fill');
        let currentIndex = 0;
        const intervalTime = 5000;
        let autoSlide;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                // Toggle visibility
                slide.style.opacity = (i === index) ? '1' : '0';
                slide.style.zIndex = (i === index) ? '10' : '0';

                // Handle Progress Bars
                fills[i].style.transition = (i === index) ? `width ${intervalTime}ms linear` : 'none';
                fills[i].style.width = (i === index) ? '100%' : '0%';
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function startTimer() {
            clearInterval(autoSlide);
            autoSlide = setInterval(nextSlide, intervalTime);
        }

        document.getElementById('nextBtn').addEventListener('click', () => {
            nextSlide();
            startTimer();
        });
        document.getElementById('prevBtn').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
            startTimer();
        });

        // Run first slide
        showSlide(0);
        startTimer();
    });
</script>