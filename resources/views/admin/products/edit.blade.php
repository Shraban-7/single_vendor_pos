@extends('admin.layouts.app')
@section('title', 'Edit Product')
@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Edit Product</h1>
            <p class="text-gray-500">Update product information</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.manage-stock', $product) }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg hover:from-purple-600 hover:to-purple-700 transition">
                <i class="fas fa-boxes mr-2"></i>Manage Stock
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>
</div>

{{-- Form --}}
<form id="productForm" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Information --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Basic Information</h2>

                {{-- Product Name --}}
                <div class="mb-5">
                    <x-input name="name" label="Product Name" required placeholder="e.g., Men's Slim Fit Cotton Shirt" value="{{ old('name', $product->name) }}" />
                </div>

                {{-- SKU --}}
                <div class="mb-5">
                    <x-input name="sku" label="SKU (Stock Keeping Unit)" placeholder="e.g., SHIRT-BLU-M-001" value="{{ old('sku', $product->sku) }}" />
                </div>

                {{-- Short Description --}}
                <div class="mb-5">
                    <x-textarea name="short_description" label="Short Description" rows="3" placeholder="Brief description for product listing">{{ old('short_description', $product->short_description) }}</x-textarea>
                </div>

                {{-- Full Description --}}
                <div>
                    <x-textarea name="description" label="Full Description" rows="6" placeholder="Detailed product description, features, and specifications">{{ old('description', $product->description) }}</x-textarea>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Pricing</h2>

                <div class="grid md:grid-cols-3 gap-5">
                    {{-- Regular Price --}}
                    <div>
                        <x-input name="price" type="number" label="Regular Price (৳)" required placeholder="0.00" value="{{ old('price', $product->price) }}" step="0.01" />
                    </div>

                    {{-- Compare Price --}}
                    <div>
                        <x-input name="compare_price" type="number" label="Compare Price (৳)" placeholder="0.00" value="{{ old('compare_price', $product->compare_price) }}" step="0.01" />
                        <p class="mt-1 text-xs text-gray-500">Original price before discount</p>
                    </div>

                    {{-- Cost Price --}}
                    <div>
                        <x-input name="cost_price" type="number" label="Cost Price (৳)" placeholder="0.00" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" />
                        <p class="mt-1 text-xs text-gray-500">For profit calculation</p>
                    </div>
                </div>
            </div>

            {{-- Product Details --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Product Details</h2>

                <div class="grid md:grid-cols-2 gap-5">
                    {{-- Brand --}}
                    <div>
                        <x-input name="brand" label="Brand" placeholder="e.g., Nike, Adidas" value="{{ old('brand', $product->brand) }}" />
                    </div>

                    {{-- Material --}}
                    <div>
                        <x-input name="material" label="Material" placeholder="e.g., 100% Cotton" value="{{ old('material', $product->material) }}" />
                    </div>

                    {{-- Fit Type --}}
                    <div>
                        <label for="fit_type" class="block text-sm font-medium text-gray-700 mb-2">Fit Type</label>
                        <select name="fit_type" id="fit_type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('fit_type') border-red-500 @enderror">
                            <option value="">Select Fit Type</option>
                            @foreach($fitTypes as $fitType)
                            <option value="{{ $fitType->value }}" {{ old('fit_type', $product->fit_type?->value) == $fitType->value ? 'selected' : '' }}>
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
                            <option value="{{ $pattern->value }}" {{ old('pattern', $product->pattern?->value) == $pattern->value ? 'selected' : '' }}>
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
                            <option value="{{ $occasion->value }}" {{ old('occasion', $product->occasion?->value) == $occasion->value ? 'selected' : '' }}>
                                {{ $occasion->label() }}
                            </option>
                            @endforeach
                        </select>
                        @error('occasion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Weight --}}
                    <div>
                        <x-input name="weight" type="number" label="Weight (grams)" placeholder="0.00" value="{{ old('weight', $product->weight) }}" step="0.01" />
                    </div>
                </div>
            </div>

            {{-- Inventory --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Inventory</h2>

                <div class="grid md:grid-cols-2 gap-5">
                    {{-- Stock Quantity --}}
                    <div>
                        <x-input name="stock_in" type="number" label="Stock Quantity" required placeholder="0" value="{{ old('stock_in', $product->stock_in) }}" />
                    </div>

                    {{-- Low Stock Threshold --}}
                    <div>
                        <x-input name="low_stock_threshold" type="number" label="Low Stock Alert" placeholder="5" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" />
                        <p class="mt-1 text-xs text-gray-500">Alert when stock reaches this level</p>
                    </div>
                </div>
            </div>

            {{-- Product Variants --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Product Variants</h2>
                    <button type="button" onclick="addVariant()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Variant
                    </button>
                </div>

                <div id="variantsContainer" class="space-y-4">
                    @forelse($product->variants as $index => $variant)
                    <div class="variant-row p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                        <div class="grid md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Size</label>
                                <select name="variants[{{ $index }}][size_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Size</option>
                                    @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" {{ $variant->size_id == $size->id ? 'selected' : '' }}>
                                        {{ $size->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                                <select name="variants[{{ $index }}][color_id]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Color</option>
                                    @foreach($colors as $color)
                                    <option value="{{ $color->id }}" {{ $variant->color_id == $color->id ? 'selected' : '' }}>
                                        {{ $color->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Price (৳)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="variants[{{ $index }}][price]"
                                    value="{{ $variant->price }}"
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00">
                            </div>


                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                                <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Optional">
                            </div>

                            <div class="flex items-end">
                                <button type="button" onclick="removeVariant(this, {{ $variant->id }})"
                                    class="w-full px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No variants added yet. Click "Add Variant" to create one.</p>
                    @endforelse
                </div>

                <input type="hidden" name="delete_variants[]" id="deleteVariants" value="">
            </div>

            {{-- SEO Settings --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">SEO Settings</h2>

                {{-- Meta Title --}}
                <div class="mb-5">
                    <x-input name="meta_title" label="Meta Title" placeholder="SEO title for search engines" value="{{ old('meta_title', $product->meta_title) }}" />
                </div>

                {{-- Meta Description --}}
                <div class="mb-5">
                    <x-textarea name="meta_description" label="Meta Description" rows="3" placeholder="SEO description for search engines">{{ old('meta_description', $product->meta_description) }}</x-textarea>
                </div>

                {{-- Tags --}}
                <div>
                    <x-input name="tags" label="Tags" placeholder="e.g., summer, casual, cotton" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" />
                    <p class="mt-1 text-xs text-gray-500">Separate tags with commas</p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Status --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Status & Visibility</h2>

                <div class="mb-5">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category['id'] }}" {{ old('category_id', $product->category_id) == $category['id'] ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Subcategory</option>
                    </select>
                </div>

                {{-- Product Status Checkboxes --}}
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active (visible to customers)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Featured Product</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">New Arrival</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale', $product->is_on_sale) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">On Sale</span>
                    </label>
                </div>
            </div>

            {{-- Product Images --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Product Media</h2>

                {{-- Thumbnail Image --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Image <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition relative">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden">
                        <div id="imagePlaceholder" class="{{ $product->image ? 'hidden' : '' }}">
                            <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">Click to upload thumbnail</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
                        </div>
                        <div id="thumbnailPreview" class="{{ $product->image ? '' : 'hidden' }}">
                            <img src="{{ $product->image ? storage_url($product->image) : '' }}" class="mx-auto h-32 object-cover rounded-lg border border-gray-200">
                            <button type="button" id="removeThumbnail" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" onclick="document.getElementById('image').click()"
                            class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition" id="thumbnailBtn">
                            {{ $product->image ? 'Change Thumbnail' : 'Choose File' }}
                        </button>
                    </div>
                    @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gallery --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (Max 5)</label>

                    {{-- Existing Images --}}
                    @if($product->images && $product->images->count() > 0)
                    <div class="mb-4">
                        <p class="text-xs text-gray-600 mb-2">Existing Images ({{ $product->images->count() }})</p>
                        <div class="grid grid-cols-2 gap-3" id="existingImagesContainer">
                            @foreach($product->images as $image)
                            <div class="relative group existing-image-item" data-image-id="{{ $image->id }}">
                                <div class="relative w-full h-32">
                                    <img src="{{ storage_url($image->image_path) }}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                                    <button type="button" class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition shadow-md remove-existing-image" data-image-id="{{ $image->id }}" title="Remove image">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="delete_images" id="deleteImages" value="">
                    </div>
                    @endif

                    {{-- New Images Upload --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                        <i class="fas fa-images text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Add more images ({{ 5 - ($product->images ? $product->images->count() : 0) }} remaining)</p>
                        <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 5MB</p>
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
                    <i class="fas fa-save mr-2"></i><span id="btnText">Update Product</span>
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


    let variantIndex = "{{ $product->variants->count() }}";
    const deleteVariantsArray = [];

    const sizes = @json($sizes);
    const colors = @json($colors);
    const productPrice = "{{ $product->price }}";

    // Add new variant
    function addVariant() {
        const container = document.getElementById('variantsContainer');

        // Remove "no variants" message if it exists
        const noVariantsMsg = container.querySelector('p.text-gray-500');
        if (noVariantsMsg) {
            noVariantsMsg.remove();
        }

        const variantRow = document.createElement('div');
        variantRow.className = 'variant-row p-4 border border-gray-200 rounded-lg bg-gray-50';

        variantRow.innerHTML = `
            <div class="grid md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Size</label>
                    <select 
                        name="variants[${variantIndex}][size_id]" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Size</option>
                        ${sizes.map(size => `
                            <option value="${size.id}">${size.name}</option>
                        `).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                    <select 
                        name="variants[${variantIndex}][color_id]" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Color</option>
                        ${colors.map(color => `
                            <option value="${color.id}">${color.name}</option>
                        `).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Price (৳)</label>
                    <input 
                        type="number"
                        step="0.01"
                        name="variants[${variantIndex}][price]"
                        required
                        value="${productPrice}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>


                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                    <input 
                        type="text"
                        name="variants[${variantIndex}][sku]"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional">
                </div>
                
                <div class="flex items-end">
                    <button 
                        type="button"
                        onclick="removeVariant(this)"
                        class="w-full px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(variantRow);
        variantIndex++;
    }

    // Remove variant
    function removeVariant(button, variantId = null) {
        if (variantId) {
            deleteVariantsArray.push(variantId);
            updateDeleteVariantsInput();
        }
        button.closest('.variant-row').remove();
    }

    // Update hidden input with variants to delete
    function updateDeleteVariantsInput() {
        const form = document.querySelector('form');
        // Remove existing delete_variants inputs
        form.querySelectorAll('input[name="delete_variants[]"]').forEach(input => input.remove());

        // Add new inputs
        deleteVariantsArray.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_variants[]';
            input.value = id;
            form.appendChild(input);
        });
    }

    // Thumbnail preview functionality
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

    // Remove thumbnail functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('#removeThumbnail')) {
            thumbnailInput.value = '';
            thumbnailPreview.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            thumbnailBtn.textContent = 'Choose File';
        }
    });

    // Gallery images logic
    const galleryInput = document.getElementById('images');
    const galleryPreviewContainer = document.getElementById('galleryPreview');
    let galleryFiles = new DataTransfer();
    const existingImagesCount = "{{ $product->images ? $product->images->count() : 0 }}";
    const deleteImagesArray = [];

    // Handle existing images removal
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-existing-image');
        if (!removeBtn) return;

        const imageId = removeBtn.dataset.imageId;
        const imageItem = removeBtn.closest('.existing-image-item');

        // Add to delete array
        deleteImagesArray.push(imageId);
        document.getElementById('deleteImages').value = JSON.stringify(deleteImagesArray);

        // Remove from DOM
        imageItem.remove();

        // Update remaining count
        const remainingCount = 5 - (document.querySelectorAll('.existing-image-item').length + galleryFiles.files.length);
        document.querySelector('.text-sm.text-gray-600').textContent = `Add more images (${remainingCount} remaining)`;
    });

    galleryInput.addEventListener('change', function(e) {
        // Calculate how many slots are available
        const currentExistingCount = document.querySelectorAll('.existing-image-item').length;
        const availableSlots = 5 - currentExistingCount - galleryFiles.files.length;
        const files = Array.from(this.files);

        if (files.length > availableSlots) {
            showToast('error', `You can only add ${availableSlots} more image(s). Maximum 5 images total.`);
            this.files = galleryFiles.files;
            return;
        }

        files.forEach(file => {
            galleryFiles.items.add(file);
        });

        // Update input with all files
        this.files = galleryFiles.files;
        renderGalleryPreviews();
    });

    // Event delegation for remove new gallery image buttons
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
                    </div>
                `;
                galleryPreviewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // AJAX Form Submission
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

                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 1500);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.status === 422 && error.data.errors) {
                    const firstError = Object.values(error.data.errors)[0][0];
                    showToast('error', firstError);
                } else {
                    const message = error.data?.message || 'An unexpected error occurred. Please try again.';
                    showToast('error', message);
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                btnText.textContent = 'Update Product';
                btnSpinner.classList.add('hidden');
            });
    });
</script>
@endpush

@endsection