{{-- Product Quick View Modal --}}
<div id="productQuickViewModal" class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4">
    <div class="modal-container bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-100 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-gray-900">Quick View</h3>
            <button onclick="closeQuickView()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>

        {{-- Modal Content --}}
        <div id="quickViewContent" class="overflow-y-auto max-h-[calc(90vh-5rem)]">
            <div class="grid md:grid-cols-2 gap-6 p-6">
                {{-- Product Image --}}
                <div class="space-y-3">
                    <div class="relative bg-gray-50 rounded-xl overflow-hidden aspect-square">
                        <img id="quickViewImage" src="{{ asset('assets/images/default.png') }}" alt="" class="w-full h-full object-cover">
                        {{-- Badge Container --}}
                        <div id="quickViewBadges" class="absolute top-3 left-3 flex flex-col gap-2"></div>
                    </div>
                    {{-- Thumbnails --}}
                    <div id="quickViewThumbnails" class="flex gap-2 overflow-x-auto hide-scrollbar pb-1"></div>
                </div>

                {{-- Product Info --}}
                <div class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <p id="quickViewBrand" class="text-sm text-brand-blue font-medium mb-1"></p>
                        <h2 id="quickViewName" class="text-xl md:text-2xl font-bold text-gray-900 mb-2"></h2>

                        {{-- Rating --}}
                        <div id="quickViewRating" class="flex items-center gap-2 mb-3"></div>
                    </div>

                    {{-- Price --}}
                    <div class="bg-gradient-to-r from-blue-50 to-brand-light rounded-xl p-4">
                        <div class="flex items-end gap-2 flex-wrap">
                            <span id="quickViewPrice" class="text-2xl md:text-3xl font-bold text-brand-blue"></span>
                            <span id="quickViewComparePrice" class="text-lg text-gray-400 line-through hidden"></span>
                            <span id="quickViewDiscount" class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-1 rounded-lg hidden"></span>
                        </div>
                    </div>

                    {{-- Stock Status --}}
                    <div id="quickViewStock" class="flex items-center gap-2"></div>

                    {{-- Variants Section --}}
                    <div id="variantsSection" class="space-y-4 hidden">
                        {{-- Color Selection --}}
                        <div id="colorSection" class="hidden">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-sm font-semibold text-gray-900">
                                    Color: <span id="selectedColorName" class="font-normal text-gray-600">Select a color</span>
                                </span>
                            </div>
                            <div id="colorOptions" class="flex gap-3 flex-wrap"></div>
                        </div>

                        {{-- Size Selection --}}
                        <div id="sizeSection" class="hidden">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-sm font-semibold text-gray-900">
                                    Size: <span id="selectedSizeName" class="font-normal text-gray-600">Select a size</span>
                                </span>
                            </div>
                            <div id="sizeOptions" class="flex gap-2.5 flex-wrap"></div>
                        </div>

                        {{-- Variant Error Message --}}
                        <div id="variantError" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="variantErrorText">Please select all options</span>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <span class="text-sm font-semibold text-gray-900 mb-3 block">Quantity</span>
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden w-fit">
                            <button onclick="updateQuickViewQuantity(-1)" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-minus text-sm"></i>
                            </button>
                            <input type="number" id="quickViewQuantity" value="1" min="1" max="999" class="w-12 h-10 text-center text-sm font-semibold border-x border-gray-200 focus:outline-none">
                            <button onclick="updateQuickViewQuantity(1)" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button id="quickViewAddToCart" onclick="addToCartFromQuickView()" class="flex-1 bg-brand-blue text-white py-3 rounded-xl font-semibold text-sm hover:bg-blue-600 transition tap-effect flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                        <button id="quickViewDetails" class="flex-1 border-2 border-brand-blue text-brand-blue py-3 rounded-xl font-semibold text-sm hover:bg-brand-blue hover:text-white transition tap-effect flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i>
                            View Details
                        </button>
                    </div>

                    {{-- Short Description --}}
                    <div id="quickViewDescription" class="text-sm text-gray-600 pt-2 border-t border-gray-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        animation: fadeIn 0.2s ease-out;
    }

    .modal-overlay.show {
        display: flex !important;
    }

    .modal-container {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    @media (max-width: 768px) {
        .modal-container {
            max-width: 95vw;
        }
    }
</style>