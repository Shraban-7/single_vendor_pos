@extends('layouts.app')

@section('title', 'Products')

@section('content')
{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ url('/') }}" class="text-gray-500 hover:text-brand-blue transition">Home</a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <span class="text-gray-900 font-medium">Products</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Mobile Filter Toggle --}}
        <div class="lg:hidden flex items-center justify-between mb-2">
            <h1 class="text-xl font-bold text-brand-black">All Products</h1>
            <button onclick="toggleMobileFilter()" class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 tap-effect">
                <i class="fas fa-sliders-h"></i>
                Filters
            </button>
        </div>

        {{-- Sidebar Filters --}}
        <aside id="filterSidebar" class="hidden lg:block w-full lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-28">

                {{-- Filter Header --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-brand-black">Filters</h2>
                    <button onclick="clearAllFilters()" class="text-sm text-brand-blue hover:underline font-medium">Clear All</button>
                </div>

                {{-- Category Filter --}}
                <div class="border-b border-gray-100 pb-5 mb-5">
                    <button onclick="toggleFilterSection('categoryFilter')" class="w-full flex items-center justify-between text-sm font-semibold text-gray-900 mb-3">
                        Category
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" id="categoryFilterIcon"></i>
                    </button>
                    <div id="categoryFilter" class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($categories as $category)
                        @php
                        $selectedCategories = request('categories', []);
                        if (!is_array($selectedCategories)) {
                        $selectedCategories = explode(',', $selectedCategories);
                        }
                        @endphp
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox"
                                name="categories[]"
                                value="{{ $category->slug }}"
                                {{ in_array($category->slug, $selectedCategories) ? 'checked' : '' }}
                                onchange="applyFilters()"
                                class="w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ $category->name }}</span>
                            <span class="ml-auto text-xs text-gray-400">({{ $categoryCounts[$category->id] ?? 0 }})</span>
                        </label>
                        @if($category->children->isNotEmpty())
                        @foreach($category->children as $child)
                        <label class="flex items-center gap-3 cursor-pointer group ml-6">
                            <input type="checkbox"
                                name="categories[]"
                                value="{{ $child->slug }}"
                                {{ in_array($child->slug, $selectedCategories) ? 'checked' : '' }}
                                onchange="applyFilters()"
                                class="w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                            <span class="text-sm text-gray-500 group-hover:text-gray-900">{{ $child->name }}</span>
                            <span class="ml-auto text-xs text-gray-400">({{ $categoryCounts[$child->id] ?? 0 }})</span>
                        </label>
                        @endforeach
                        @endif
                        @endforeach
                    </div>
                </div>

                {{-- Price Range Filter --}}
                <div class="border-b border-gray-100 pb-5 mb-5">
                    <button onclick="toggleFilterSection('priceFilter')" class="w-full flex items-center justify-between text-sm font-semibold text-gray-900 mb-3">
                        Price Range
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" id="priceFilterIcon"></i>
                    </button>
                    <div id="priceFilter" class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Min</label>
                                <input type="number"
                                    name="min_price"
                                    value="{{ request('min_price') }}"
                                    placeholder="৳0"
                                    onchange="applyFilters()"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue">
                            </div>
                            <span class="text-gray-400 mt-5">-</span>
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Max</label>
                                <input type="number"
                                    name="max_price"
                                    value="{{ request('max_price') }}"
                                    placeholder="৳10000"
                                    onchange="applyFilters()"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="price" class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Under ৳500</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="price" class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">৳500 - ৳1000</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="price" class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">৳1000 - ৳2000</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="price" class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">৳2000 - ৳5000</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="price" class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Above ৳5000</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Size Filter --}}
                <div class="border-b border-gray-100 pb-5 mb-5">
                    <button onclick="toggleFilterSection('sizeFilter')" class="w-full flex items-center justify-between text-sm font-semibold text-gray-900 mb-3">
                        Size
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" id="sizeFilterIcon"></i>
                    </button>
                    <div id="sizeFilter" class="flex flex-wrap gap-2">
                        @php
                        $selectedSizes = request('sizes', []);
                        if (!is_array($selectedSizes)) {
                        $selectedSizes = explode(',', $selectedSizes);
                        }
                        @endphp
                        @foreach($sizes->whereIn('code', ['xs', 's', 'm', 'l', 'xl', 'xxl', '3xl']) as $size)
                        <button
                            type="button"
                            onclick="toggleFilter('sizes[]', '{{ $size->id }}')"
                            class="size-btn px-4 py-2 border {{ in_array($size->id, $selectedSizes) ? 'border-brand-blue bg-brand-blue/5 text-brand-blue' : 'border-gray-200 text-gray-600' }} rounded-lg text-sm font-medium hover:border-brand-blue hover:text-brand-blue transition">
                            {{ $size->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Color Filter --}}
                <div class="border-b border-gray-100 pb-5 mb-5">
                    <button onclick="toggleFilterSection('colorFilter')" class="w-full flex items-center justify-between text-sm font-semibold text-gray-900 mb-3">
                        Color
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" id="colorFilterIcon"></i>
                    </button>
                    <div id="colorFilter" class="flex flex-wrap gap-2">
                        @php
                        $selectedColors = request('colors', []);
                        if (!is_array($selectedColors)) {
                        $selectedColors = explode(',', $selectedColors);
                        }
                        @endphp
                        @foreach($colors->take(15) as $color)
                        <button
                            type="button"
                            onclick="toggleFilter('colors[]', '{{ $color->id }}')"
                            title="{{ $color->name }}"
                            class="w-8 h-8 rounded-full border-2 {{ in_array($color->id, $selectedColors) ? 'border-brand-blue ring-2 ring-brand-blue/30' : 'border-gray-300' }} hover:border-brand-blue transition relative"
                            style="background-color: {{ $color->hex_code ?? '#CCCCCC' }}">
                            @if(in_array($color->id, $selectedColors))
                            <i class="fas fa-check text-xs absolute inset-0 flex items-center justify-center" style="color: {{ $color->code == 'white' || $color->code == 'cream' ? '#000' : '#FFF' }}"></i>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Rating Filter --}}
                <div class="pb-2">
                    <button onclick="toggleFilterSection('ratingFilter')" class="w-full flex items-center justify-between text-sm font-semibold text-gray-900 mb-3">
                        Rating
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" id="ratingFilterIcon"></i>
                    </button>
                    <div id="ratingFilter" class="space-y-2">
                        @foreach([5, 4, 3, 2, 1] as $rating)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio"
                                name="min_rating"
                                value="{{ $rating }}"
                                {{ request('min_rating') == $rating ? 'checked' : '' }}
                                onchange="applyFilters()"
                                class="w-4 h-4 border-gray-300 text-brand-blue focus:ring-brand-blue">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= $rating ? '' : 'text-gray-300' }}"></i>
                                    @endfor
                            </div>
                            <span class="text-xs text-gray-400">& up</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Apply Filter Button (Mobile) --}}
                <button onclick="toggleMobileFilter()" class="lg:hidden w-full bg-brand-blue text-white py-3 rounded-xl font-semibold text-sm mt-4 tap-effect">
                    Apply Filters
                </button>

            </div>
        </aside>

        {{-- Products Grid Section --}}
        <div class="flex-1">

            {{-- Products Header --}}
            <div class="hidden lg:flex items-center justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-brand-black">
                        @if(request('category'))
                        {{ ucfirst(request('category')) }} Products
                        @elseif(request('search'))
                        Search Results
                        @else
                        All Products
                        @endif
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                    </p>
                </div>
                <div class="flex items-center gap-3">

                    {{-- Sort Dropdown --}}
                    <div class="relative">
                        <select onchange="window.location.href=this.value" class="appearance-none bg-white border border-gray-200 rounded-xl py-2.5 pl-4 pr-10 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue cursor-pointer">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}" {{ request('sort') == 'featured' ? 'selected' : '' }}>Sort by: Featured</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'best_selling']) }}" {{ request('sort') == 'best_selling' ? 'selected' : '' }}>Best Selling</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'top_rated']) }}" {{ request('sort') == 'top_rated' ? 'selected' : '' }}>Top Rated</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- Mobile Sort --}}
            <div class="lg:hidden flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">{{ $products->total() }} products</p>
                <select onchange="window.location.href=this.value" class="appearance-none bg-white border border-gray-200 rounded-lg py-2 pl-3 pr-8 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}" {{ request('sort') == 'featured' ? 'selected' : '' }}>Sort: Featured</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>

            {{-- Active Filters --}}
            @php
            $hasFilters = request()->hasAny(['categories', 'sizes', 'colors', 'min_price', 'max_price', 'min_rating']);
            $selectedCategories = request('categories', []);
            if (!is_array($selectedCategories)) {
            $selectedCategories = explode(',', $selectedCategories);
            }
            $selectedSizes = request('sizes', []);
            if (!is_array($selectedSizes)) {
            $selectedSizes = explode(',', $selectedSizes);
            }
            $selectedColors = request('colors', []);
            if (!is_array($selectedColors)) {
            $selectedColors = explode(',', $selectedColors);
            }
            @endphp

            @if($hasFilters)
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="text-sm text-gray-500 font-medium">Active Filters:</span>

                {{-- Category Filters --}}
                @foreach($selectedCategories as $categorySlug)
                @php
                $category = $categories->firstWhere('slug', $categorySlug);
                @endphp
                @if($category)
                <span class="inline-flex items-center gap-1.5 bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $category->name }}
                    <button onclick="removeFilter('categories', '{{ $categorySlug }}')" class="hover:text-blue-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
                @endif
                @endforeach

                {{-- Size Filters --}}
                @foreach($selectedSizes as $sizeId)
                @php
                $size = $sizes->firstWhere('id', $sizeId);
                @endphp
                @if($size)
                <span class="inline-flex items-center gap-1.5 bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-full text-sm font-medium">
                    Size: {{ $size->name }}
                    <button onclick="removeFilter('sizes', '{{ $sizeId }}')" class="hover:text-blue-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
                @endif
                @endforeach

                {{-- Color Filters --}}
                @foreach($selectedColors as $colorId)
                @php
                $color = $colors->firstWhere('id', $colorId);
                @endphp
                @if($color)
                <span class="inline-flex items-center gap-1.5 bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-full text-sm font-medium">
                    <span class="w-3 h-3 rounded-full border border-gray-300" style="background-color: {{ $color->hex_code ?? '#CCCCCC' }}"></span>
                    {{ $color->name }}
                    <button onclick="removeFilter('colors', '{{ $colorId }}')" class="hover:text-blue-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
                @endif
                @endforeach

                {{-- Price Range Filter --}}
                @if(request('min_price') || request('max_price'))
                <span class="inline-flex items-center gap-1.5 bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-full text-sm font-medium">
                    Price:
                    @if(request('min_price') && request('max_price'))
                    ৳{{ number_format(request('min_price')) }} - ৳{{ number_format(request('max_price')) }}
                    @elseif(request('min_price'))
                    Above ৳{{ number_format(request('min_price')) }}
                    @else
                    Below ৳{{ number_format(request('max_price')) }}
                    @endif
                    <button onclick="removeFilter('price')" class="hover:text-blue-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
                @endif

                {{-- Rating Filter --}}
                @if(request('min_rating'))
                <span class="inline-flex items-center gap-1.5 bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-full text-sm font-medium">
                    <div class="flex text-yellow-500">
                        @for($i = 1; $i <= request('min_rating'); $i++)
                            <i class="fas fa-star text-xs"></i>
                            @endfor
                    </div>
                    & up
                    <button onclick="removeFilter('min_rating')" class="hover:text-blue-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
                @endif

                {{-- Clear All Button --}}
                <button onclick="clearAllFilters()" class="text-sm text-red-500 hover:text-red-600 hover:underline font-medium ml-2 transition">
                    Clear All
                </button>
            </div>
            @endif

            {{-- Products Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse ($products as $product)
                <x-product-card :product="$product" />
                @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search criteria.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-center gap-2 mt-8">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</div>

{{-- Mobile Filter Overlay --}}
<div id="mobileFilterOverlay" class="fixed inset-0 bg-black/50 z-[60] hidden lg:hidden" onclick="toggleMobileFilter()"></div>
@endsection

@push('scripts')
<script>
    // Apply filters - collect all filter values and redirect with query params
    function applyFilters() {
        const url = new URL(window.location.href);
        const searchParams = new URLSearchParams();

        // Keep existing sort and search params
        if (url.searchParams.get('sort')) {
            searchParams.set('sort', url.searchParams.get('sort'));
        }
        if (url.searchParams.get('search')) {
            searchParams.set('search', url.searchParams.get('search'));
        }

        // Categories
        const categories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(cb => cb.value);
        if (categories.length > 0) {
            searchParams.set('categories', categories.join(','));
        }

        // Sizes
        const sizes = Array.from(document.querySelectorAll('input[name="sizes[]"]:checked'))
            .map(cb => cb.value);
        if (sizes.length > 0) {
            searchParams.set('sizes', sizes.join(','));
        }

        // Colors
        const colors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked'))
            .map(cb => cb.value);
        if (colors.length > 0) {
            searchParams.set('colors', colors.join(','));
        }

        // Price range
        const minPrice = document.querySelector('input[name="min_price"]')?.value;
        const maxPrice = document.querySelector('input[name="max_price"]')?.value;
        if (minPrice) searchParams.set('min_price', minPrice);
        if (maxPrice) searchParams.set('max_price', maxPrice);

        // Rating
        const rating = document.querySelector('input[name="min_rating"]:checked')?.value;
        if (rating) searchParams.set('min_rating', rating);

        // Redirect with filters
        window.location.href = `${url.pathname}?${searchParams.toString()}`;
    }

    // Remove a specific filter
    function removeFilter(filterType, value = null) {
        const url = new URL(window.location.href);
        const searchParams = new URLSearchParams(url.searchParams);

        if (filterType === 'categories' || filterType === 'sizes' || filterType === 'colors') {
            // Get current values
            const currentValues = searchParams.get(filterType);
            if (currentValues) {
                const valuesArray = currentValues.split(',').filter(v => v !== value);

                if (valuesArray.length > 0) {
                    searchParams.set(filterType, valuesArray.join(','));
                } else {
                    searchParams.delete(filterType);
                }
            }
        } else if (filterType === 'price') {
            // Remove both min and max price
            searchParams.delete('min_price');
            searchParams.delete('max_price');
        } else {
            // Remove single parameter (like min_rating)
            searchParams.delete(filterType);
        }

        // Redirect with updated filters
        window.location.href = `${url.pathname}?${searchParams.toString()}`;
    }

    // Clear all filters
    function clearAllFilters() {
        const url = new URL(window.location.href);
        const searchParams = new URLSearchParams();

        // Keep only sort and search parameters
        if (url.searchParams.get('sort')) {
            searchParams.set('sort', url.searchParams.get('sort'));
        }
        if (url.searchParams.get('search')) {
            searchParams.set('search', url.searchParams.get('search'));
        }

        // Redirect without filters
        const queryString = searchParams.toString();
        window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
    }

    // Toggle filter checkbox (for size and color buttons)
    function toggleFilter(name, value) {
        // Check if a checkbox already exists for this filter
        let checkbox = document.querySelector(`input[name="${name}"][value="${value}"]`);

        if (!checkbox) {
            // Create hidden checkbox
            checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = name;
            checkbox.value = value;
            checkbox.style.display = 'none';
            document.getElementById('filterSidebar').appendChild(checkbox);
        }

        checkbox.checked = !checkbox.checked;
        applyFilters();
    }

    // Toggle mobile filter sidebar
    function toggleMobileFilter() {
        const sidebar = document.getElementById('filterSidebar');
        const overlay = document.getElementById('mobileFilterOverlay');

        sidebar.classList.toggle('hidden');
        overlay.classList.toggle('hidden');

        if (!sidebar.classList.contains('hidden')) {
            sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-[70]', 'w-80', 'max-w-[85vw]', 'overflow-y-auto', 'bg-white', 'shadow-2xl');
            sidebar.classList.remove('lg:block');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-[70]', 'w-80', 'max-w-[85vw]', 'overflow-y-auto', 'bg-white', 'shadow-2xl');
            sidebar.classList.add('lg:block');
            document.body.style.overflow = '';
        }
    }

    // Toggle filter section collapse
    function toggleFilterSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.getElementById(sectionId + 'Icon');

        section.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
</script>
@endpush