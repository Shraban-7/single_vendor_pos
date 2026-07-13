@extends('admin.layouts.app')
@section('title', 'Edit Product')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Edit Product</h1>
        <p class="text-xs text-slate-500">Modify active metadata parameters and inventory metrics for this catalog item.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.manage-stock', $product) }}" class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
            <span>Manage Stock</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Products</span>
        </a>
    </div>
</div>

<form id="productForm" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="grid lg:grid-cols-3 gap-4">
        {{-- Main Workspace Column --}}
        <div class="lg:col-span-2 space-y-4 text-xs">

            {{-- Basic Information Card --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Basic Information</h2>

                <div>
                    <x-input name="name" label="Product Name *" required placeholder="e.g., Men's Slim Fit Cotton Shirt" value="{{ old('name', $product->name) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                <div>
                    <x-input name="sku" label="SKU (Stock Keeping Unit)" placeholder="e.g., SHIRT-BLU-M-001" value="{{ old('sku', $product->sku) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                <div>
                    <x-textarea name="short_description" label="Short Description" rows="2" placeholder="Brief metadata summary for product directory lists" class="text-xs bg-slate-50/50 focus:bg-white">{{ old('short_description', $product->short_description) }}</x-textarea>
                </div>

                <div>
                    <x-textarea name="description" label="Full Detail Description" rows="4" placeholder="Comprehensive profile specifications and product summary logs" class="text-xs bg-slate-50/50 focus:bg-white">{{ old('description', $product->description) }}</x-textarea>
                </div>
            </div>

            {{-- Pricing Parameters --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Pricing Matrix Parameters</h2>

                <div class="grid md:grid-cols-3 gap-3">
                    <div>
                        <x-input name="price" type="number" label="Regular Price (৳) *" required placeholder="0.00" value="{{ old('price', $product->price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="compare_price" type="number" label="Compare Price (৳)" placeholder="0.00" value="{{ old('compare_price', $product->compare_price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Original price before markdown discounts</p>
                    </div>
                    <div>
                        <x-input name="cost_price" type="number" label="Cost Price (৳)" placeholder="0.00" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Base inventory expense tracking metric</p>
                    </div>
                </div>
            </div>

            {{-- Product Metadata Specifications --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Product Metadata Specifications</h2>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="material" label="Material Composition" placeholder="e.g., 100% Cotton" value="{{ old('material', $product->material) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="weight" type="number" label="Weight Metrics (grams)" placeholder="0.00" value="{{ old('weight', $product->weight) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>

                    {{-- Fit Type Selector --}}
                    <div>
                        <label for="fit_type" class="block font-semibold text-slate-600 mb-1">Fit Target Cut Profile</label>
                        <select name="fit_type" id="fit_type" class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('fit_type') border-rose-500 @enderror">
                            <option value="">Select Cut Variant</option>
                            @foreach($fitTypes as $fitType)
                                <option value="{{ $fitType->value }}" {{ old('fit_type', $product->fit_type?->value) == $fitType->value ? 'selected' : '' }}>
                                    {{ $fitType->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('fit_type') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pattern Selector --}}
                    <div>
                        <label for="pattern" class="block font-semibold text-slate-600 mb-1">Pattern Structure Profile</label>
                        <select name="pattern" id="pattern" class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('pattern') border-rose-500 @enderror">
                            <option value="">Select Layout Design</option>
                            @foreach($patterns as $pattern)
                                <option value="{{ $pattern->value }}" {{ old('pattern', $product->pattern?->value) == $pattern->value ? 'selected' : '' }}>
                                    {{ $pattern->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('pattern') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Occasion Selector --}}
                    <div class="md:col-span-2">
                        <label for="occasion" class="block font-semibold text-slate-600 mb-1">Seasonal Occasion Type</label>
                        <select name="occasion" id="occasion" class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('occasion') border-rose-500 @enderror">
                            <option value="">Select Event Match</option>
                            @foreach($occasions as $occasion)
                                <option value="{{ $occasion->value }}" {{ old('occasion', $product->occasion?->value) == $occasion->value ? 'selected' : '' }}>
                                    {{ $occasion->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('occasion') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Stock Inventory Operations --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Inventory Operations Storage</h2>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="stock_in" type="number" label="Stock Quantity *" required placeholder="0" value="{{ old('stock_in', $product->stock_in) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="low_stock_threshold" type="number" label="Low Inventory Alert Level" placeholder="5" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Trigger warnings when reserves hit this density mark</p>
                    </div>
                </div>
            </div>

            {{-- SEO Optimization Engine --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">SEO Engine Optimization Architecture</h2>

                <div>
                    <x-input name="meta_title" label="Meta Title Tag" placeholder="Optimized title string headers" value="{{ old('meta_title', $product->meta_title) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                <div>
                    <x-textarea name="meta_description" label="Meta Description Snippet" rows="2" placeholder="Structured description metadata excerpts">{{ old('meta_description', $product->meta_description) }}</x-textarea>
                </div>

                <div>
                    <x-input name="tags" label="Catalog Search Tags Map" placeholder="e.g., summer, casual, cotton" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <p class="mt-1 text-[10px] text-slate-400">Separate values with commas</p>
                </div>
            </div>
        </div>

        {{-- Sidebar Configurations Column --}}
        <div class="space-y-4 text-xs">

            {{-- Status & Visibility Options --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Taxonomy & Global Visibility</h2>

                {{-- Parent Category Node --}}
                <div>
                    <label for="category_id" class="block font-semibold text-slate-600 mb-1">Parent Category Namespace <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('category_id') border-rose-500 @enderror">
                        <option value="">Select Root Node</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('category_id', $product->category_id) == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subcategory Branch Node --}}
                <div>
                    <label for="subcategory_id" class="block font-semibold text-slate-600 mb-1">Subcategory Branch Node</label>
                    <select name="subcategory_id" id="subcategory_id" class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                        <option value="">Select Subcategory</option>
                    </select>
                </div>

                {{-- Visibility Status Checkboxes --}}
                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Active Visibility Flag</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Featured Placement Focus</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">New Arrival Segment Marker</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Best Seller Rank Indexer</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale', $product->is_on_sale) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Active Markdown Sale Promo</span>
                    </label>
                </div>
            </div>

            {{-- Asset Media Cards Engine --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Asset Catalog Media</h2>

                {{-- Hero Thumbnail File Upload Field --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1.5">Hero Thumbnail Image <span class="text-rose-500">*</span></label>
                    <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition relative">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden">

                        <div id="imagePlaceholder" class="{{ $product->image ? 'hidden' : 'py-2' }}">
                            <i data-lucide="image" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                            <p class="text-slate-600 font-semibold mb-0.5">Click target window area to upload thumbnail</p>
                            <p class="text-[10px] text-slate-400">PNG, JPG, WEBP up to 2MB</p>
                        </div>

                        <div id="thumbnailPreview" class="{{ $product->image ? 'relative' : 'hidden' }}">
                            <img src="{{ $product->image ? storage_url($product->image) : '' }}" class="mx-auto h-24 object-cover rounded border border-slate-200">
                            <button type="button" id="removeThumbnail" class="absolute top-1 right-1 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow shadow-rose-200 transition">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>

                        <button type="button" onclick="document.getElementById('image').click()" class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition" id="thumbnailBtn">
                            {{ $product->image ? 'Change Thumbnail' : 'Choose File' }}
                        </button>
                    </div>
                    @error('image') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Secondary Gallery Manager Grid --}}
                <div class="border-t border-slate-100 pt-3">
                    <label class="block font-semibold text-slate-600 mb-1.5">Secondary Gallery Array (Max 5)</label>

                    {{-- Existing Multi Images Previews --}}
                    @if($product->images && $product->images->count() > 0)
                        <div class="mb-3">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide font-semibold mb-1.5">Currently Mounted Images ({{ $product->images->count() }})</p>
                            <div class="grid grid-cols-3 gap-2" id="existingImagesContainer">
                                @foreach($product->images as $image)
                                    <div class="relative group existing-image-item" data-image-id="{{ $image->id }}">
                                        <div class="relative w-full h-20">
                                            <img src="{{ storage_url($image->image_path) }}" class="w-full h-full object-cover rounded border border-slate-200">
                                            <button type="button" class="absolute -top-1.5 -right-1.5 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow shadow-rose-200 transition remove-existing-image" data-image-id="{{ $image->id }}" title="Remove index image">
                                                <i data-lucide="x" class="w-2.5 h-2.5"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="delete_images" id="deleteImages" value="">
                        </div>
                    @endif

                    {{-- New Files Buffer Upload Section --}}
                    <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition">
                        <i data-lucide="images" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                        <p class="text-slate-600 font-semibold mb-0.5">Add more images ({{ 5 - ($product->images ? $product->images->count() : 0) }} slots remaining)</p>
                        <p class="text-[10px] text-slate-400">PNG, JPG, WEBP bounds maximum 5MB scope size</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">

                        <button type="button" onclick="document.getElementById('images').click()" class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition">
                            Choose Files
                        </button>
                    </div>
                    <div id="galleryPreview" class="mt-3 grid grid-cols-3 gap-2"></div>
                    @error('images') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Form Operations Control Action Panel --}}
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm flex flex-col gap-2">
                <button type="submit" id="submitBtn" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm focus:outline-none">
                    <span id="btnText">Update Product Specs</span>
                    <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin hidden" id="btnSpinner"></i>
                </button>
                <a href="{{ route('admin.products.index') }}" class="w-full h-9 inline-flex items-center justify-center text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                    Cancel Operation
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const CATEGORIES = @json($categories);

    const catId = "{{ old('category_id', $product->category_id) }}";
    const subcatId = "{{ old('subcategory_id', $product->subcategory_id ?? 'null') }}";

    document.addEventListener('DOMContentLoaded', function() {
        if (catId) {
            loadSubcategories(catId);
        }
    });

    document.getElementById('category_id').addEventListener('change', function() {
        loadSubcategories(this.value);
    });

    function loadSubcategories(categoryId) {
        const subcategorySelect = document.getElementById('subcategory_id');
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

        const category = CATEGORIES.find(cat => cat.id == categoryId);
        if (category && category.children) {
            category.children.forEach(subcat => {
                const option = document.createElement('option');
                option.value = subcat.id;
                option.textContent = subcat.name;
                if (subcat.id == subcatId) {
                    option.selected = true;
                }
                subcategorySelect.appendChild(option);
            });
        }
    }

    // Thumbnail Preview Engine
    const thumbnailInput = document.getElementById('image');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailBtn = document.getElementById('thumbnailBtn');

    thumbnailInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                thumbnailPreview.querySelector('img').src = e.target.result;
                thumbnailPreview.classList.remove('hidden');
                imagePlaceholder.classList.add('hidden');
                thumbnailBtn.textContent = 'Change Thumbnail';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Reset Thumbnail Drop Attachment
    document.addEventListener('click', function(e) {
        if (e.target.closest('#removeThumbnail')) {
            thumbnailInput.value = '';
            thumbnailPreview.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            thumbnailBtn.textContent = 'Choose File';
        }
    });

    // Gallery Matrix Upload Handler Pipeline
    const galleryInput = document.getElementById('images');
    const galleryPreviewContainer = document.getElementById('galleryPreview');
    let galleryFiles = new DataTransfer();
    const deleteImagesArray = [];

    // Existing Database Image Eviction Handler Mapping
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-existing-image');
        if (!removeBtn) return;

        const imageId = removeBtn.dataset.imageId;
        const imageItem = removeBtn.closest('.existing-image-item');

        deleteImagesArray.push(imageId);
        document.getElementById('deleteImages').value = JSON.stringify(deleteImagesArray);
        imageItem.remove();

        updateRemainingCountText();
    });

    galleryInput.addEventListener('change', function(e) {
        const currentExistingCount = document.querySelectorAll('.existing-image-item').length;
        const availableSlots = 5 - currentExistingCount - galleryFiles.files.length;
        const files = Array.from(this.files);

        if (files.length > availableSlots) {
            alert(`You can only append ${availableSlots} more image(s). Maximum 5 images across total catalog allowance.`);
            this.files = galleryFiles.files;
            return;
        }

        files.forEach(file => {
            galleryFiles.items.add(file);
        });

        this.files = galleryFiles.files;
        renderGalleryPreviews();
        updateRemainingCountText();
    });

    // Splice targeted index position file out of new attachment collection
    galleryPreviewContainer.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-gallery-image');
        if (!btn) return;

        const indexToRemove = parseInt(btn.dataset.index);
        const dt = new DataTransfer();

        Array.from(galleryFiles.files).forEach((file, i) => {
            if (i !== indexToRemove) {
                dt.items.add(file);
            }
        });

        galleryFiles = dt;
        galleryInput.files = galleryFiles.files;
        renderGalleryPreviews();
        updateRemainingCountText();
    });

    function updateRemainingCountText() {
        const currentExistingCount = document.querySelectorAll('.existing-image-item').length;
        const remainingCount = 5 - currentExistingCount - galleryFiles.files.length;
        const targetLabel = document.querySelector('p.text-slate-600');
        if (targetLabel) {
            targetLabel.textContent = `Add more images (${remainingCount} slots remaining)`;
        }
    }

    function renderGalleryPreviews() {
        galleryPreviewContainer.innerHTML = '';
        if (galleryFiles.files.length === 0) return;

        Array.from(galleryFiles.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <div class="relative w-full h-20">
                        <img src="${e.target.result}" class="w-full h-full object-cover rounded border border-slate-200">
                        <button type="button" class="absolute -top-1.5 -right-1.5 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow transition remove-gallery-image" data-index="${index}" title="Remove image">
                            <i data-lucide="x" class="w-2.5 h-2.5"></i>
                        </button>
                    </div>
                `;
                galleryPreviewContainer.appendChild(div);
                if(window.lucide) lucide.createIcons();
            };
            reader.readAsDataURL(file);
        });
    }

    // Form Submission Pipeline via Ajax
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    productForm.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        btnText.textContent = 'Updating...';
        btnSpinner.classList.remove('hidden');

        const formData = new FormData(productForm);

        fetch(productForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw { status: response.status, data: data }; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if(window.showToast) showToast('success', data.message);
                setTimeout(() => {
                    if (data.redirect) window.location.href = data.redirect;
                }, 1000);
            } else {
                if(window.showToast) showToast('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.status === 422 && error.data.errors) {
                const firstError = Object.values(error.data.errors)[0][0];
                if(window.showToast) showToast('error', firstError);
            } else {
                const message = error.data?.message || 'An operational system exception occurred.';
                if(window.showToast) showToast('error', message);
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = 'Update Product Specs';
            btnSpinner.classList.add('hidden');
        });
    });
</script>
@endpush

@endsection
