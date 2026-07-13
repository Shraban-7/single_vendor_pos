@extends('admin.layouts.app')
@section('title', 'Add Product')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Add New Product</h1>
        <p class="text-xs text-slate-500">Publish a new variants package item directly into the active product catalog matrix.</p>
    </div>
    <div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Products</span>
        </a>
    </div>
</div>

<form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div class="grid lg:grid-cols-3 gap-4">
        {{-- Main Content Workspace Column --}}
        <div class="lg:col-span-2 space-y-4 text-xs">
            {{-- Basic Information Card --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Basic Information</h2>

                <div>
                    <x-input name="name" label="Product Name *" required placeholder="e.g., Men's Slim Fit Cotton Shirt" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                <div>
                    <x-input name="sku" label="SKU (Stock Keeping Unit)" placeholder="e.g., SHIRT-BLU-M-001" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <p class="mt-1 text-[10px] text-slate-400">Leave empty to auto-generate a unique catalog track tag code</p>
                </div>

                <div>
                    <x-textarea name="short_description" label="Short Description" rows="2" placeholder="Brief metadata copy summary for general search listings pages" class="text-xs bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <x-textarea name="description" label="Full Detail Description" rows="4" placeholder="Detailed core descriptions, specifications, context and overview data points" class="text-xs bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            {{-- Pricing Parameters Card --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Pricing Matrix Parameters</h2>

                <div class="grid md:grid-cols-3 gap-3">
                    <div>
                        <x-input name="price" label="Regular Price (৳) *" placeholder="0.00" required class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="compare_price" label="Compare Price (৳)" placeholder="0.00" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">MSRP retail strike price before discount</p>
                    </div>
                    <div>
                        <x-input name="cost_price" label="Cost Price (৳)" placeholder="0.00" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Base inventory cost for net profit yield audits</p>
                    </div>
                </div>
            </div>

            {{-- Product Technical Specifications --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Product Metadata Specifications</h2>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="material" label="Material Composition" placeholder="e.g., 100% Cotton" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="weight" label="Weight Metrics (grams)" type="number" step="0.01" placeholder="0.00" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    {{-- Fit Type Selector --}}
                    <div>
                        <label for="fit_type" class="block font-semibold text-slate-600 mb-1">Fit Target Cut Profile</label>
                        <select name="fit_type" id="fit_type"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('fit_type') border-rose-500 @enderror">
                            <option value="">Select Cut Variant</option>
                            @foreach($fitTypes as $fitType)
                                <option value="{{ $fitType->value }}" {{ old('fit_type') == $fitType->value ? 'selected' : '' }}>
                                    {{ $fitType->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('fit_type') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pattern Selector --}}
                    <div>
                        <label for="pattern" class="block font-semibold text-slate-600 mb-1">Pattern Structure Profile</label>
                        <select name="pattern" id="pattern"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('pattern') border-rose-500 @enderror">
                            <option value="">Select Layout Design</option>
                            @foreach($patterns as $pattern)
                                <option value="{{ $pattern->value }}" {{ old('pattern') == $pattern->value ? 'selected' : '' }}>
                                    {{ $pattern->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('pattern') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Occasion Selector --}}
                    <div class="md:col-span-2">
                        <label for="occasion" class="block font-semibold text-slate-600 mb-1">Seasonal Occasion Type</label>
                        <select name="occasion" id="occasion"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('occasion') border-rose-500 @enderror">
                            <option value="">Select Event Match</option>
                            @foreach($occasions as $occasion)
                                <option value="{{ $occasion->value }}" {{ old('occasion') == $occasion->value ? 'selected' : '' }}>
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
                        <x-input name="stock_in" label="Initial Stock Quantity *" type="number" required placeholder="0" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="low_stock_threshold" label="Low Inventory Restock Alert Level" type="number" value="{{ old('low_stock_threshold', 5) }}" placeholder="5" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Trigger warnings when reserves drop below this value</p>
                    </div>
                </div>
            </div>

            {{-- Optimization Architecture --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">SEO Engine Optimization Architecture</h2>
                <div>
                    <x-input name="meta_title" label="Meta Title Tag" placeholder="Optimized SEO title string header fields" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <x-textarea name="meta_description" label="Meta Description String snippet" placeholder="Structured excerpt indexing summaries for crawl spiders description text" class="text-xs bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <x-input name="tags" label="Catalog Search Tags Map" placeholder="e.g., summer, casual, cotton" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <p class="mt-1 text-[10px] text-slate-400">Delineate tags via simple comma separations</p>
                </div>
            </div>
        </div>

        {{-- Sidebar Configurations Column --}}
        <div class="space-y-4 text-xs">
            {{-- Category / Visibility Options --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Taxonomy & Global Visibility</h2>

                {{-- Root Category Select --}}
                <div>
                    <label for="category_id" class="block font-semibold text-slate-600 mb-1">Parent Category Namespace <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('category_id') border-rose-500 @enderror">
                        <option value="">Select Root Node</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Child Subcategory Select --}}
                <div>
                    <label for="subcategory_id" class="block font-semibold text-slate-600 mb-1">Subcategory Branch Node</label>
                    <select name="subcategory_id" id="subcategory_id"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('subcategory_id') border-rose-500 @enderror">
                        <option value="">Select Subcategory</option>
                    </select>
                    @error('subcategory_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Custom Segment Flag Toggles --}}
                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Active Visibility Flag</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Featured Placement Focus</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival') ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">New Arrival Segment Marker</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller') ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Best Seller Rank Indexer</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale') ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Active Markdown Sale Promo</span>
                    </label>
                </div>
            </div>

            {{-- Main Media Engine --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Asset Catalog Media</h2>

                {{-- Hero Thumbnail File Slot --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1.5">Hero Thumbnail Image <span class="text-rose-500">*</span></label>
                    <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition relative">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden">

                        <div id="imagePlaceholder" class="py-2">
                            <i data-lucide="image" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                            <p class="text-slate-600 font-semibold mb-0.5">Click container target space to upload asset</p>
                            <p class="text-[10px] text-slate-400">PNG, JPG or webp payloads up to 2MB</p>
                        </div>

                        <div id="imagePreview" class="hidden relative">
                            <img src="" class="mx-auto h-24 object-cover rounded border border-slate-200">
                            <button type="button" id="removeimage" class="absolute top-1 right-1 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow shadow-rose-200 transition">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>

                        <button type="button" onclick="document.getElementById('image').click()"
                            class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition" id="imageBtn">
                            Choose File
                        </button>
                    </div>
                    @error('image') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Secondary Gallery Grid Slots --}}
                <div class="border-t border-slate-100 pt-3">
                    <label class="block font-semibold text-slate-600 mb-1.5">Secondary Gallery Array (Limit 5)</label>
                    <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition">
                        <i data-lucide="images" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                        <p class="text-slate-600 font-semibold mb-0.5">Drop files direct or click trigger button</p>
                        <p class="text-[10px] text-slate-400">Multi PNG, JPG bounds maximum 5MB scope size</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">

                        <button type="button" onclick="document.getElementById('images').click()"
                            class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition">
                            Choose Files
                        </button>
                    </div>
                    <div id="galleryPreview" class="mt-3 grid grid-cols-2 gap-2"></div>
                    @error('images') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Form Operations Trigger Block --}}
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm flex flex-col gap-2">
                <button type="submit" id="submitBtn" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm focus:outline-none">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span id="btnText">Save Product Variant</span>
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
    const subcatId = "{{ old('subcategory_id', 'null') }}";

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

    // Core Single Thumbnail Preview Handling
    const imgInput = document.getElementById('image');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imagePreview = document.getElementById('imagePreview');
    const imageBtn = document.getElementById('imageBtn');

    imgInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.classList.remove('hidden');
                imagePlaceholder.classList.add('hidden');
                imageBtn.textContent = 'Change image';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Reset Thumbnail Attachment Hook
    document.addEventListener('click', function(e) {
        if (e.target.closest('#removeimage')) {
            imgInput.value = '';
            imagePreview.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            imageBtn.textContent = 'Choose File';
        }
    });

    // Array Gallery Files Buffer Core Logic
    const galleryInput = document.getElementById('images');
    const galleryPreviewContainer = document.getElementById('galleryPreview');
    let galleryFiles = new DataTransfer();

    galleryInput.addEventListener('change', function(e) {
        const newFiles = Array.from(this.files);
        if (newFiles.length === 0) return;

        const currentFiles = Array.from(galleryFiles.files);
        if (newFiles.length === currentFiles.length &&
            newFiles.every((f, i) => f.name === currentFiles[i].name && f.size === currentFiles[i].size)) {
            return;
        }

        const uniqueNewFiles = newFiles.filter(file =>
            !currentFiles.some(existing => existing.name === file.name && existing.size === file.size)
        );

        if (galleryFiles.files.length + uniqueNewFiles.length > 5) {
            alert('Maximum 5 images allowed for gallery.');
            this.files = galleryFiles.files;
            return;
        }

        const dt = new DataTransfer();
        currentFiles.forEach(file => dt.items.add(file));
        uniqueNewFiles.forEach(file => dt.items.add(file));

        galleryFiles = dt;
        this.files = galleryFiles.files;
        renderGalleryPreviews();
    });

    // Splice target index out of file buffer mapping
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
    });

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

    // Ajax Form Payload Execution Pipeline
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    productForm.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        btnText.textContent = 'Saving...';
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
                productForm.reset();
                galleryPreviewContainer.innerHTML = '';
                galleryFiles = new DataTransfer();
                imgInput.value = '';
                imagePreview.classList.add('hidden');
                imagePlaceholder.classList.remove('hidden');
                imageBtn.textContent = 'Choose File';

                setTimeout(() => {
                    if (data.redirect) window.location.href = data.redirect;
                }, 1000);
            } else {
                if(window.showToast) showToast('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errMsg = error.data?.message || 'An unexpected validation exception occurred.';
            if(window.showToast) showToast('error', errMsg);
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = 'Save Product Variant';
            btnSpinner.classList.add('hidden');
        });
    });
</script>
@endpush

@endsection
