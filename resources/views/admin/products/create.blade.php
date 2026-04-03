@extends('admin.layouts.app')
@section('title', 'Add Product')
@section('content')

<div class="mb-3">
    <div class="flex items-center justify-between">
        <h1 class="text-lg md:text-2xl font-bold text-gray-900 mb-0">Add New Product</h1>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>
</div>

<form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Basic Information</h2>
                <div class="mb-5">
                    <x-input name="name" label="Product Name" required placeholder="e.g., Men's Slim Fit Cotton Shirt" />
                </div>

                <div class="mb-5">
                    <x-input name="sku" label="SKU (Stock Keeping Unit)" placeholder="e.g., SHIRT-BLU-M-001" />
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate</p>
                </div>

                <div class="mb-5">
                    <x-textarea name="short_description" label="Short Description" rows="3" placeholder="Brief description for product listing" />
                </div>
                <div>
                    <x-textarea name="description" label="Full Description" rows="6" placeholder="Detailed product description, features, and specifications" />
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Pricing</h2>

                <div class="grid md:grid-cols-3 gap-5">
                    <div>
                        <x-input name="price" label="Regular Price (৳)" placeholder="0.00" required />
                    </div>
                    <div>
                        <x-input name="compare_price" label="Compare Price (৳)" placeholder="0.00" />
                        <p class="mt-1 text-xs text-gray-500">Original price before discount</p>
                    </div>
                    <div>
                        <x-input name="cost_price" label="Cost Price (৳)" placeholder="0.00" />
                        <p class="mt-1 text-xs text-gray-500">For profit calculation</p>
                    </div>
                </div>
            </div>

            {{-- Product Details --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Product Details</h2>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <x-input name="brand" label="Brand" placeholder="e.g., Nike, Adidas" />
                    </div>
                    <div>
                        <x-input name="material" label="Material" placeholder="e.g., 100% Cotton" />
                    </div>
                    {{-- Fit Type --}}
                    <div>
                        <label for="fit_type" class="block text-sm font-medium text-gray-700 mb-2">Fit Type</label>
                        <select name="fit_type" id="fit_type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('fit_type') border-red-500 @enderror">
                            <option value="">Select Fit Type</option>
                            @foreach($fitTypes as $fitType)
                            <option value="{{ $fitType->value }}" {{ old('fit_type') == $fitType->value ? 'selected' : '' }}>
                                {{ $fitType->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('fit_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pattern --}}
                    <div>
                        <label for="pattern" class="block text-sm font-medium text-gray-700 mb-2">Pattern</label>
                        <select name="pattern" id="pattern"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('pattern') border-red-500 @enderror">
                            <option value="">Select Pattern</option>
                            @foreach($patterns as $pattern)
                            <option value="{{ $pattern->value }}" {{ old('pattern') == $pattern->value ? 'selected' : '' }}>
                                {{ $pattern->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('pattern')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Occasion --}}
                    <div>
                        <label for="occasion" class="block text-sm font-medium text-gray-700 mb-2">Occasion</label>
                        <select name="occasion" id="occasion"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('occasion') border-red-500 @enderror">
                            <option value="">Select Occasion</option>
                            @foreach($occasions as $occasion)
                            <option value="{{ $occasion->value }}" {{ old('occasion') == $occasion->value ? 'selected' : '' }}>
                                {{ $occasion->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('occasion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input name="weight" label="Weight (grams)" type="number" step="0.01" placeholder="0.00" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Inventory</h2>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <x-input name="stock_in" label="Stock Quantity" type="number" required placeholder="0" />
                    </div>
                    <div>
                        <x-input name="low_stock_threshold" label="Low Stock Alert" type="number" value="{{ old('low_stock_threshold', 5) }}" placeholder="5" />
                        <p class="mt-1 text-xs text-gray-500">Alert when stock reaches this level</p>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">SEO Settings</h2>
                <div class="mb-5">
                    <x-input name="meta_title" label="Meta Title" placeholder="SEO title for search engines" />
                </div>
                <div class="mb-5">
                    <x-textarea name="meta_description" label="Meta Description" placeholder="SEO description for search engines" />
                </div>
                <div>
                    <x-input name="tags" label="Tags" placeholder="e.g., summer, casual, cotton" />
                    <p class="mt-1 text-xs text-gray-500">Separate tags with commas</p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Status --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Status & Visibility</h2>

                {{-- Category --}}
                <div class="mb-5">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('subcategory_id') border-red-500 @enderror">
                        <option value="">Select Subcategory</option>
                       
                    </select>
                    @error('subcategory_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Product Status Checkboxes --}}
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active (visible to customers)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Featured Product</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">New Arrival</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">On Sale</span>
                    </label>
                </div>
            </div>

            {{-- Product Media --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Product Media</h2>

                {{-- image --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">image Image <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition relative">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden">
                        <div id="imagePlaceholder">
                            <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">Click to upload image</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
                        </div>
                        <div id="imagePreview" class="hidden">
                            <img src="" class="mx-auto h-32 object-cover rounded-lg border border-gray-200">
                            <button type="button" id="removeimage" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" onclick="document.getElementById('image').click()"
                            class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition" id="imageBtn">
                            Choose File
                        </button>
                    </div>
                    @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gallery --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (Max 5)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                        <i class="fas fa-images text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Drop images here or click to upload</p>
                        <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 5MB. Max 5 images.</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('images').click()"
                            class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            Choose Files
                        </button>
                    </div>
                    <div id="galleryPreview" class="mt-4 grid grid-cols-2 gap-3"></div>
                    @error('images')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <button type="submit" id="submitBtn" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:ring-4 focus:ring-blue-300 transition">
                    <i class="fas fa-plus mr-2"></i><span id="btnText">Save Product</span>
                    <i class="fas fa-spinner fa-spin ml-2 hidden" id="btnSpinner"></i>
                </button>
                <a href="{{ route('admin.products.index') }}" class="block w-full px-6 py-3 text-center text-gray-700 font-medium bg-gray-100 rounded-lg hover:bg-gray-200 transition mt-3">
                    Cancel
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
    
    // image preview functionality
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

    // Remove image functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('#removeimage')) {
            imgInput.value = '';
            imagePreview.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            imageBtn.textContent = 'Choose File';
        }
    });

    // Gallery images logic
    const galleryInput = document.getElementById('images');
    const galleryPreviewContainer = document.getElementById('galleryPreview');
    let galleryFiles = new DataTransfer();

    galleryInput.addEventListener('change', function(e) {
        // Get the newly selected files
        const newFiles = Array.from(this.files);

        // If no files selected, ignore
        if (newFiles.length === 0) return;

        // If the new selection is exactly the same as our stored state, it's likely our own update
        const currentFiles = Array.from(galleryFiles.files);
        if (newFiles.length === currentFiles.length &&
            newFiles.every((f, i) => f.name === currentFiles[i].name && f.size === currentFiles[i].size)) {
            return;
        }

        // Check if adding these files would exceed the limit
        // Note: We only count NEW unique files that aren't already in the gallery
        const uniqueNewFiles = newFiles.filter(file =>
            !currentFiles.some(existing => existing.name === file.name && existing.size === file.size)
        );

        if (galleryFiles.files.length + uniqueNewFiles.length > 5) {
            showToast('error', 'Maximum 5 images allowed for gallery.');
            // Restore previous valid state
            this.files = galleryFiles.files;
            return;
        }

        // Create a new DataTransfer to hold the combined list
        const dt = new DataTransfer();

        // Add existing files
        currentFiles.forEach(file => dt.items.add(file));

        // Add new unique files
        uniqueNewFiles.forEach(file => {
            dt.items.add(file);
        });

        // Update our state
        galleryFiles = dt;

        // Update the input to hold all currently selected files
        this.files = galleryFiles.files;

        renderGalleryPreviews();
    });

    // Event delegation for remove buttons
    galleryPreviewContainer.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-gallery-image');
        if (!btn) return;

        const indexToRemove = parseInt(btn.dataset.index);
        const dt = new DataTransfer();

        // Rebuild DataTransfer excluding the removed file
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
                    <div class="relative w-full h-32">
                        <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                        <button type="button" class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition shadow-md remove-gallery-image" data-index="${index}" title="Remove image">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                        <span class="absolute bottom-2 left-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded">Image ${index + 1}</span>
                    </div>
                `;
                galleryPreviewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

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
                    return response.json().then(data => {
                        throw {
                            status: response.status,
                            data: data
                        };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);

                    productForm.reset();
                    document.getElementById('imagePreview').innerHTML = ''; // Reset managed gallery files
                    galleryFiles = new DataTransfer();
                    // Reset image preview
                    document.getElementById('image').value = '';
                    document.getElementById('imagePreview').classList.add('hidden');
                    document.getElementById('imagePlaceholder').classList.remove('hidden');
                    document.getElementById('imageBtn').textContent = 'Choose File';

                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 2000);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.status === 422 && error.data.errors) {
                    showToast('error', 'error.data.errors');
                } else {
                    const message = error.data?.message || 'An unexpected error occurred. Please try again.';
                    showToast('error', message);
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.textContent = 'Save Product';
                btnSpinner.classList.add('hidden');
            });
    });

    document.getElementById('name').addEventListener('input', function(e) {
        const name = e.target.value;
    });
</script>
@endpush

@endsection