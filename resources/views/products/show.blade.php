@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name)

@section('content')
{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <nav class="flex items-center gap-2 text-sm overflow-x-auto hide-scrollbar whitespace-nowrap">
            <a href="{{ url('/') }}" class="text-gray-500 hover:text-brand-blue transition flex-shrink-0">Home</a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-brand-blue transition flex-shrink-0">Products</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 flex-shrink-0"></i>
            @if($product->category)
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-gray-500 hover:text-brand-blue transition flex-shrink-0">{{ $product->category->name }}</a>
            <i class="fas fa-chevron-right text-xs text-gray-400 flex-shrink-0"></i>
            @endif
            <span class="text-gray-900 font-medium flex-shrink-0">{{ $product->name }}</span>
        </nav>
    </div>
</div>

<div>
    <div class="max-w-7xl mx-auto p-4">
        {{-- Product Main Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 mb-8">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">

                {{-- Product Images --}}
                <div class="space-y-4">
                    {{-- Main Image --}}
                    <div class="relative bg-white rounded-2xl overflow-hidden border border-gray-100">
                        <img id="mainProductImage" src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-[400px] sm:h-[500px] lg:h-[600px] object-cover">

                        {{-- Badges --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($product->is_new_arrival)
                            <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">NEW ARRIVAL</span>
                            @endif
                            @if($product->discount_percentage)
                            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">-{{ $product->discount_percentage }}% OFF</span>
                            @endif
                            @if($product->is_best_seller)
                            <span class="bg-yellow-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">BEST SELLER</span>
                            @endif
                        </div>

                        {{-- Wishlist Button --}}
                        <button class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-50 transition tap-effect">
                            <i class="far fa-heart text-gray-600 text-lg"></i>
                        </button>

                        {{-- Zoom Button --}}
                        <button onclick="openImageModal()" class="absolute bottom-4 right-4 w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition tap-effect">
                            <i class="fas fa-search-plus text-gray-600"></i>
                        </button>
                    </div>

                    {{-- Thumbnail Images --}}
                    @if($product->images->count() > 1)
                    <div class="w-full max-w-full overflow-x-auto hide-scrollbar pb-2">
                        <div class="flex gap-3 w-max">
                            @foreach($product->images as $index => $image)
                            <button onclick="changeMainImage(this)" class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-brand-blue' : 'border-transparent hover:border-brand-blue' }} transition">
                                <img src="{{ storage_url($image->image_path) }}" alt="{{ $product->name }} - Image {{ $loop->iteration }}" class="w-full h-full object-cover">
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="space-y-4 min-w-0 overflow-hidden">
                    <div>
                        <p class="text-xs text-brand-blue font-medium mb-1.5">{{ $product->brand ?? '' }} @if($product->category)• {{ $product->category->name }}@endif</p>
                        <h1 class="text-xl md:text-2xl font-bold text-brand-black mb-2">{{ $product->name }}</h1>

                        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <=floor($product->average_rating))
                                        <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $product->average_rating)
                                            <i class="fas fa-star-half-alt"></i>
                                            @else
                                            <i class="far fa-star text-gray-300"></i>
                                            @endif
                                            @endfor
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">{{ number_format($product->average_rating, 1) }}</span>
                                <span class="text-xs sm:text-sm text-gray-400">({{ $product->review_count }} {{ Str::plural('review', $product->review_count) }})</span>
                            </div>
                            <span class="text-gray-300 hidden sm:inline">|</span>
                            @if($product->totalStock > 0)
                            <span class="text-xs sm:text-sm text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>In Stock</span>
                            @else
                            <span class="text-xs sm:text-sm text-red-600 font-medium"><i class="fas fa-times-circle mr-1"></i>Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="flex flex-wrap items-end gap-2 mb-1.5">
                        <span id="productPrice" class="text-2xl font-bold text-brand-blue" data-base-price="{{ $product->price }}">{{ money($product->price) }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                        <span id="productComparePrice" class="text-lg sm:text-xl text-gray-400 line-through" data-base-compare="{{ $product->compare_price }}">{{ money($product->compare_price) }}</span>
                        <span id="productSavings" class="bg-red-100 text-red-600 text-xs sm:text-sm font-semibold px-2 py-1 rounded-lg">Save {{ money($product->compare_price - $product->price) }}</span>
                        @else
                        <span id="productComparePrice" class="text-lg sm:text-xl text-gray-400 line-through hidden" data-base-compare="{{ $product->compare_price ?? 0 }}"></span>
                        <span id="productSavings" class="bg-red-100 text-red-600 text-xs sm:text-sm font-semibold px-2 py-1 rounded-lg hidden"></span>
                        @endif
                    </div>

                    {{-- Color Selection --}}
                    @if($availableColors->count() > 0)
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-sm font-semibold text-gray-900">Color: <span id="selectedColorName" class="font-normal text-gray-600">Select a color</span></span>
                        </div>
                        <div class="flex gap-3 flex-wrap">
                            @foreach($availableColors as $color)
                            @if($color->hex_code)
                            <button onclick="selectColor(this, '{{ $color->name }}')"
                                data-color-id="{{ $color->id }}"
                                class="color-btn w-11 h-11 rounded-full border-2 border-gray-300 hover:border-brand-blue transition p-1 shadow-sm"
                                style="background-color: {{ $color->hex_code }};"
                                title="{{ $color->name }}">
                            </button>
                            @else
                            <button onclick="selectColor(this, '{{ $color->name }}')"
                                data-color-id="{{ $color->id }}"
                                class="color-btn w-11 h-11 rounded-full border-2 border-gray-300 hover:border-brand-blue transition p-1 shadow-sm flex items-center justify-center text-xs text-gray-600"
                                title="{{ $color->name }}">
                                {{ strtoupper(substr($color->name, 0, 3)) }}
                            </button>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Size Selection --}}
                    @if($availableSizes->count() > 0)
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-sm font-semibold text-gray-900">Size: <span id="selectedSizeName" class="font-normal text-gray-600">Select a size</span></span>
                            <button class="text-xs text-brand-blue hover:underline font-medium">Size Guide</button>
                        </div>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($availableSizes as $size)
                            @php
                            $variant = $product->variants->where('size_id', $size->id)->first();
                            $isAvailable = $variant && $variant->stock_in > 0;
                            @endphp
                            <button onclick="selectSize(this, '{{ $size->name }}')"
                                data-size-id="{{ $size->id }}"
                                class="product-size-btn min-w-[56px] h-9 px-3 border border-gray-300 rounded-lg text-sm font-medium transition
                                       {{ $isAvailable ? 'text-gray-700 hover:border-brand-blue hover:text-brand-blue hover:bg-brand-blue/5' : 'border-gray-200 text-gray-300 cursor-not-allowed' }}"
                                {{ $isAvailable ? '' : 'disabled' }}>
                                {{ $size->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Quantity --}}
                    <div>
                        <span class="text-sm font-semibold text-gray-900 mb-2.5 block">Quantity</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center border-2 border-gray-300 rounded-xl overflow-hidden">
                                <button onclick="updateQuantity(-1)" class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="productQuantity" value="1" min="1" max="{{ $product->stock_in }}" class="w-16 h-12 text-center text-base font-semibold border-x-2 border-gray-300 focus:outline-none">
                                <button onclick="updateQuantity(1)" class="w-12 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if($product->isLowStock())
                            <span class="text-sm text-gray-500">Only <span class="text-orange-500 font-semibold">{{ $product->stock_in }} items</span> left!</span>
                            @elseif($product->stock_in > 0)
                            <span class="text-sm text-gray-500"><span class="text-green-600 font-semibold">{{ $product->stock_in }} items</span> available</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2 w-full">
                        @if($product->totalStock > 0)
                        <button id="addToCartBtn" class="w-full sm:flex-1 bg-brand-blue text-white py-3 rounded-xl font-bold text-base hover:bg-blue-600 transition tap-effect shadow-lg shadow-brand-blue/25 flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart text-lg"></i>
                            Add to Cart
                        </button>
                        <button id="buyNowBtn" class="w-full sm:flex-1 bg-brand-black text-white py-3 rounded-xl font-bold text-base hover:bg-gray-800 transition tap-effect flex items-center justify-center gap-2">
                            <i class="fas fa-bolt text-lg"></i>
                            Buy Now
                        </button>
                        @else
                        <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-bold text-base cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-times-circle text-lg"></i>
                            Out of Stock
                        </button>
                        @endif
                    </div>

                    {{-- Delivery Info --}}
                    <div class="bg-gray-50 rounded-2xl p-5 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-truck text-brand-blue"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm">Free Delivery</h4>
                                <p class="text-xs text-gray-500">Enter your postal code for delivery availability</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-undo text-brand-blue"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm">7 Days Return</h4>
                                <p class="text-xs text-gray-500">Free returns within 7 days of delivery</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-brand-blue"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm">Secure Payment</h4>
                                <p class="text-xs text-gray-500">100% secure payment with bKash, Nagad, Card</p>
                            </div>
                        </div>
                    </div>

                    {{-- Share --}}
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium text-gray-600">Share:</span>
                        <div class="flex gap-2">
                            <a href="#" class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition">
                                <i class="fab fa-facebook-f text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center text-white hover:bg-sky-600 transition">
                                <i class="fab fa-twitter text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp text-xs"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-300 transition">
                                <i class="fas fa-link text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Details Tabs --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 mb-8">
            <div>
                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200">
                    <div class="flex gap-8 overflow-x-auto hide-scrollbar">
                        <button onclick="switchProductTab('description')" class="product-tab pb-4 text-sm font-semibold text-brand-blue border-b-2 border-brand-blue whitespace-nowrap" data-tab="description">
                            Description
                        </button>
                        <button onclick="switchProductTab('specifications')" class="product-tab pb-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent whitespace-nowrap" data-tab="specifications">
                            Specifications
                        </button>
                        <button onclick="switchProductTab('reviews')" class="product-tab pb-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent whitespace-nowrap" data-tab="reviews">
                            Reviews ({{ $product->review_count }})
                        </button>
                        <button onclick="switchProductTab('shipping')" class="product-tab pb-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent whitespace-nowrap" data-tab="shipping">
                            Shipping & Returns
                        </button>
                    </div>
                </div>

                {{-- Tab Content --}}
                <div class="py-8">
                    {{-- Description Tab --}}
                    <div id="descriptionTab" class="product-tab-content">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Product Description</h3>
                            @if($product->short_description)
                            <p class="text-gray-600 mb-4">
                                {{ $product->short_description }}
                            </p>
                            @endif
                            @if($product->description)
                            <div class="text-gray-600">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                            @else
                            <p class="text-gray-600 mb-4">
                                Introducing our {{ $product->name }} - crafted with premium quality materials for exceptional comfort and style.
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Specifications Tab --}}
                    <div id="specificationsTab" class="product-tab-content hidden">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Product Specifications</h3>
                        <div class="bg-gray-50 rounded-2xl overflow-hidden">
                            <table class="w-full">
                                <tbody>
                                    @if($product->brand)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100 w-1/3">Brand</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->brand }}</td>
                                    </tr>
                                    @endif
                                    @if($product->sku)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">SKU</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->sku }}</td>
                                    </tr>
                                    @endif
                                    @if($product->material)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Material</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->material }}</td>
                                    </tr>
                                    @endif
                                    @if($product->fit_type)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Fit Type</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->fit_type->value }}</td>
                                    </tr>
                                    @endif
                                    @if($product->pattern)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Pattern</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->pattern->value }}</td>
                                    </tr>
                                    @endif
                                    @if($product->occasion)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Occasion</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->occasion->value }}</td>
                                    </tr>
                                    @endif
                                    @if($availableSizes->count() > 0)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Available Sizes</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $availableSizes->pluck('name')->join(', ') }}</td>
                                    </tr>
                                    @endif
                                    @if($availableColors->count() > 0)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Available Colors</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $availableColors->pluck('name')->join(', ') }}</td>
                                    </tr>
                                    @endif
                                    @if($product->weight)
                                    <tr>
                                        <td class="py-4 px-5 text-sm font-medium text-gray-600 bg-gray-100">Weight</td>
                                        <td class="py-4 px-5 text-sm text-gray-900">{{ $product->weight }} kg</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Reviews Tab --}}
                    <div id="reviewsTab" class="product-tab-content hidden">
                        <div class="grid lg:grid-cols-3 gap-8">
                            {{-- Rating Summary --}}
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 rounded-2xl p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">Customer Reviews</h3>
                                    <div class="text-center mb-6">
                                        <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($product->average_rating, 1) }}</div>
                                        <div class="flex justify-center text-yellow-400 mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <=floor($product->average_rating))
                                                <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $product->average_rating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                    <i class="far fa-star text-gray-300"></i>
                                                    @endif
                                                    @endfor
                                        </div>
                                        <p class="text-sm text-gray-500">Based on {{ $product->review_count }} {{ Str::plural('review', $product->review_count) }}</p>
                                    </div>

                                    {{-- Rating Bars --}}
                                    <div class="space-y-3">
                                        @foreach([5, 4, 3, 2, 1] as $rating)
                                        @php
                                        $count = $ratingDistribution[$rating] ?? 0;
                                        $percentage = $product->review_count > 0 ? ($count / $product->review_count * 100) : 0;
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm text-gray-600 w-8">{{ $rating }} ★</span>
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-500 w-10">{{ $count }}</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    <button class="w-full mt-6 bg-brand-blue text-white py-3 rounded-xl font-semibold text-sm hover:bg-blue-600 transition tap-effect">
                                        Write a Review
                                    </button>
                                </div>
                            </div>

                            {{-- Reviews List --}}
                            <div class="lg:col-span-2 space-y-6">
                                @forelse($product->approvedReviews->take(10) as $review)
                                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-brand-blue rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-sm">{{ $review->user->name ?? 'Anonymous' }}</h4>
                                                <p class="text-xs text-gray-400">Verified Purchase • {{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ $review->comment }}
                                    </p>
                                    @if($review->images && $review->images->count() > 0)
                                    <div class="flex gap-2 mb-3">
                                        @foreach($review->images->take(4) as $image)
                                        <img src="{{ $image->image_url }}" alt="Review image" class="w-16 h-16 rounded-lg object-cover">
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="flex items-center gap-4 text-sm">
                                        <button class="text-gray-500 hover:text-brand-blue transition flex items-center gap-1">
                                            <i class="far fa-thumbs-up"></i> Helpful
                                        </button>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-12">
                                    <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Reviews Yet</h3>
                                    <p class="text-gray-500">Be the first to review this product!</p>
                                </div>
                                @endforelse

                                @if($product->approvedReviews->count() > 10)
                                <button class="w-full border border-gray-200 text-gray-600 py-3 rounded-xl font-medium text-sm hover:bg-gray-50 transition">
                                    Load More Reviews
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Tab --}}
                    <div id="shippingTab" class="product-tab-content hidden">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Shipping Information</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-truck text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">Standard Delivery</h4>
                                            <p class="text-sm text-gray-600">3-5 business days • ৳60</p>
                                            <p class="text-xs text-gray-500 mt-1">Free on orders over ৳2000</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-shipping-fast text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">Express Delivery</h4>
                                            <p class="text-sm text-gray-600">1-2 business days • ৳120</p>
                                            <p class="text-xs text-gray-500 mt-1">Available in Dhaka only</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-store text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">Store Pickup</h4>
                                            <p class="text-sm text-gray-600">Same day • Free</p>
                                            <p class="text-xs text-gray-500 mt-1">Bashundhara City, Dhaka</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Return Policy</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-undo text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">7 Days Easy Return</h4>
                                            <p class="text-sm text-gray-600">Return within 7 days of delivery for a full refund or exchange.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-exchange-alt text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">Free Exchange</h4>
                                            <p class="text-sm text-gray-600">Exchange for a different size or color at no extra cost.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-info-circle text-brand-blue text-lg mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm">Return Conditions</h4>
                                            <p class="text-sm text-gray-600">Item must be unused, unwashed, and in original packaging with tags attached.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl md:text-xl font-bold text-brand-black">You May Also Like</h2>
                <a href="{{ route('products.index') }}" class="text-brand-blue text-sm font-semibold flex items-center gap-1 tap-effect">
                    View All
                    <i class="fas fa-chevron-right text-sm"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 md:grid-cols-5 gap-4 mb-4">
                @foreach($relatedProducts as $relatedProduct)
                <x-product-card :product="$relatedProduct" />
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/90" onclick="closeImageModal()"></div>
    <button onclick="closeImageModal()" class="absolute top-4 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white z-10 transition">
        <i class="fas fa-times text-xl"></i>
    </button>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <img id="modalImage" src="" alt="Product Image" class="max-w-full max-h-full object-contain">
    </div>
</div>
@endsection

@push('scripts')
<script>
    const maxQuantity = "{{ $product->stock_in ?? 0 }}";
    const productVariants = @json($product->variants);

    // Change main product image
    function changeMainImage(btn) {
        const img = btn.querySelector('img');
        const mainImage = document.getElementById('mainProductImage');

        // Update main image
        mainImage.src = img.src;

        // Update thumbnail borders
        document.querySelectorAll('.flex.gap-3 button, .w-max button').forEach(b => {
            b.classList.remove('border-brand-blue');
            b.classList.add('border-transparent');
        });
        btn.classList.remove('border-transparent');
        btn.classList.add('border-brand-blue');
    }

    // Open image modal
    function openImageModal() {
        const mainImage = document.getElementById('mainProductImage');
        const modalImage = document.getElementById('modalImage');
        const modal = document.getElementById('imageModal');

        modalImage.src = mainImage.src;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close image modal
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Update quantity
    function updateQuantity(change) {
        const input = document.getElementById('productQuantity');
        let value = parseInt(input.value) + change;
        if (value < 1) value = 1;
        if (value > maxQuantity) value = maxQuantity;
        input.value = value;
    }

    // Select color
    function selectColor(btn, colorName) {
        document.querySelectorAll('.color-btn').forEach(b => {
            b.classList.remove('border-brand-blue');
            b.classList.add('border-gray-300');
        });
        btn.classList.remove('border-gray-300');
        btn.classList.add('border-brand-blue');
        document.getElementById('selectedColorName').textContent = colorName;
        updateVariantPrice();
    }

    // Select size
    function selectSize(btn, sizeName) {
        document.querySelectorAll('.product-size-btn:not([disabled])').forEach(b => {
            b.classList.remove('border-brand-blue', 'bg-brand-blue/5', 'text-brand-blue', 'font-semibold');
            b.classList.add('border-gray-300', 'text-gray-700', 'font-medium');
        });
        btn.classList.remove('border-gray-300', 'text-gray-700', 'font-medium');
        btn.classList.add('border-brand-blue', 'bg-brand-blue/5', 'text-brand-blue', 'font-semibold');
        document.getElementById('selectedSizeName').textContent = sizeName;
        updateVariantPrice();
    }

    // Switch product tabs
    function switchProductTab(tab) {
        // Update tab buttons
        document.querySelectorAll('.product-tab').forEach(t => {
            t.classList.remove('text-brand-blue', 'border-brand-blue', 'font-semibold');
            t.classList.add('text-gray-500', 'border-transparent', 'font-medium');
        });
        document.querySelector(`[data-tab="${tab}"]`).classList.remove('text-gray-500', 'border-transparent', 'font-medium');
        document.querySelector(`[data-tab="${tab}"]`).classList.add('text-brand-blue', 'border-brand-blue', 'font-semibold');

        // Update tab content
        document.querySelectorAll('.product-tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById(tab + 'Tab').classList.remove('hidden');
    }

    // Size button toggle (legacy support)
    document.querySelectorAll('.product-size-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.product-size-btn').forEach(b => {
                b.classList.remove('border-brand-blue', 'bg-brand-blue/5', 'text-brand-blue', 'font-semibold');
                if (!b.disabled) {
                    b.classList.add('border-gray-300', 'text-gray-700', 'font-medium');
                }
            });
            this.classList.remove('border-gray-300', 'text-gray-700', 'font-medium');
            this.classList.add('border-brand-blue', 'bg-brand-blue/5', 'text-brand-blue', 'font-semibold');
        });
    });

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Update variant price
    function updateVariantPrice() {
        const selectedColorBtn = document.querySelector('.color-btn.border-brand-blue');
        const selectedSizeBtn = document.querySelector('.product-size-btn.border-brand-blue');

        if (!productVariants || productVariants.length === 0) {
            return;
        }

        // Get selected IDs
        const colorId = selectedColorBtn ? parseInt(selectedColorBtn.dataset.colorId) : null;
        const sizeId = selectedSizeBtn ? parseInt(selectedSizeBtn.dataset.sizeId) : null;

        // Get unique colors and sizes from variants
        const colors = [...new Map(productVariants.filter(v => v.color).map(v => [v.color.id, v.color])).values()];
        const sizes = [...new Map(productVariants.filter(v => v.size).map(v => [v.size.id, v.size])).values()];
        const hasColors = colors.length > 0;
        const hasSizes = sizes.length > 0;

        // Find matching variant
        const variant = productVariants.find(v => {
            const colorMatch = !hasColors || v.color_id === colorId;
            const sizeMatch = !hasSizes || v.size_id === sizeId;
            return colorMatch && sizeMatch;
        });

        const priceElement = document.getElementById('productPrice');
        const comparePriceElement = document.getElementById('productComparePrice');
        const savingsElement = document.getElementById('productSavings');
        const basePrice = parseFloat(priceElement.dataset.basePrice);
        const baseCompare = parseFloat(comparePriceElement.dataset.baseCompare || 0);

        // Use variant price if available and not zero
        if (variant && variant.price && variant.price > 0) {
            priceElement.textContent = `৳${Number(variant.price).toLocaleString()}`;

            if (variant.compare_price && variant.compare_price > variant.price) {
                comparePriceElement.textContent = `৳${Number(variant.compare_price).toLocaleString()}`;
                comparePriceElement.classList.remove('hidden');
                const savings = variant.compare_price - variant.price;
                savingsElement.textContent = `Save ৳${Number(savings).toLocaleString()}`;
                savingsElement.classList.remove('hidden');
            } else {
                comparePriceElement.classList.add('hidden');
                savingsElement.classList.add('hidden');
            }
        } else {
            // Fallback to base price
            priceElement.textContent = `৳${Number(basePrice).toLocaleString()}`;
            if (baseCompare && baseCompare > basePrice) {
                comparePriceElement.textContent = `৳${Number(baseCompare).toLocaleString()}`;
                comparePriceElement.classList.remove('hidden');
                const savings = baseCompare - basePrice;
                savingsElement.textContent = `Save ৳${Number(savings).toLocaleString()}`;
                savingsElement.classList.remove('hidden');
            } else {
                comparePriceElement.classList.add('hidden');
                savingsElement.classList.add('hidden');
            }
        }
    }

    // Add to cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartBtn = document.getElementById('addToCartBtn');
        const buyNowBtn = document.getElementById('buyNowBtn');

        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const result = getSelectedVariant();

                // Check for variant validation errors
                if (result === 'error_color') {
                    if (window.showError) {
                        window.showError('Please select a color');
                    }
                    return;
                }

                if (result === 'error_size') {
                    if (window.showError) {
                        window.showError('Please select a size');
                    }
                    return;
                }

                if (result === 'error_not_found') {
                    if (window.showError) {
                        window.showError('Selected variant is not available');
                    }
                    return;
                }

                const quantity = parseInt(document.getElementById('productQuantity').value);

                if (window.cartManager) {
                    window.cartManager.addToCart("{{$product->id}}", result, quantity).then(success => {
                        if (success && window.openCartDrawer) {
                            window.openCartDrawer();
                        }
                    });
                }
            });
        }

        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', function() {
                const result = getSelectedVariant();

                // Check for variant validation errors
                if (result === 'error_color') {
                    if (window.showError) {
                        window.showError('Please select a color');
                    }
                    return;
                }

                if (result === 'error_size') {
                    if (window.showError) {
                        window.showError('Please select a size');
                    }
                    return;
                }

                if (result === 'error_not_found') {
                    if (window.showError) {
                        window.showError('Selected variant is not available');
                    }
                    return;
                }

                const quantity = parseInt(document.getElementById('productQuantity').value);

                if (window.cartManager) {
                    window.cartManager.addToCart("{{$product->id}}", result, quantity).then(success => {
                        if (success) {
                            window.location.href = "{{ route('checkout.index') }}";
                        }
                    });
                }
            });
        }

        // Get selected variant based on color and size
        function getSelectedVariant() {
            const selectedColorBtn = document.querySelector('.color-btn.border-brand-blue');
            const selectedSizeBtn = document.querySelector('.product-size-btn.border-brand-blue');

            // If no variants exist, return null
            if (!productVariants || productVariants.length === 0) {
                return null;
            }

            // Get unique colors and sizes from variants
            const colors = [...new Map(productVariants.filter(v => v.color).map(v => [v.color.id, v.color])).values()];
            const sizes = [...new Map(productVariants.filter(v => v.size).map(v => [v.size.id, v.size])).values()];

            const hasColors = colors.length > 0;
            const hasSizes = sizes.length > 0;

            // Validate selections
            if (hasColors && !selectedColorBtn) {
                return 'error_color';
            }

            if (hasSizes && !selectedSizeBtn) {
                return 'error_size';
            }

            // Get selected IDs
            const colorId = selectedColorBtn ? parseInt(selectedColorBtn.dataset.colorId) : null;
            const sizeId = selectedSizeBtn ? parseInt(selectedSizeBtn.dataset.sizeId) : null;

            // Find matching variant
            const variant = productVariants.find(v => {
                const colorMatch = !hasColors || v.color_id === colorId;
                const sizeMatch = !hasSizes || v.size_id === sizeId;
                return colorMatch && sizeMatch;
            });

            return variant ? variant.id : 'error_not_found';
        }
    });
</script>
@endpush