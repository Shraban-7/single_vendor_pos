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
                    <x-input name="name" label="Product Name *" required placeholder="e.g., Premium Cotton Shirt" value="{{ old('name', $product->name) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="sku" label="SKU (Stock Keeping Unit)" placeholder="Auto-generated if empty" value="{{ old('sku', $product->sku) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="barcode" label="Barcode" placeholder="e.g., 8801234567890" value="{{ old('barcode', $product->barcode) }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                </div>

                <div>
                    <x-textarea name="description" label="Full Detail Description" rows="4" placeholder="Comprehensive profile specifications and product summary logs" class="text-xs bg-slate-50/50 focus:bg-white">{{ old('description', $product->description) }}</x-textarea>
                </div>
            </div>

            {{-- Pricing Parameters --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Pricing Matrix Parameters</h2>

                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="selling_price" type="number" label="Selling Price (৳) *" required placeholder="0.00" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="cost_price" type="number" label="Cost Price (৳) *" required placeholder="0.00" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Base inventory expense tracking metric</p>
                    </div>
                    <div>
                        <x-input name="wholesale_price" type="number" label="Wholesale Price (৳)" placeholder="0.00" value="{{ old('wholesale_price', $product->wholesale_price) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="vat_rate" type="number" label="VAT Rate (%)" placeholder="0.00" value="{{ old('vat_rate', $product->vat_rate) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Configurations Column --}}
        <div class="space-y-4 text-xs">

            {{-- Taxonomy & Settings --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Taxonomy & Settings</h2>

                {{-- Category Node --}}
                <div>
                    <label for="category_id" class="block font-semibold text-slate-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('category_id') border-rose-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('category_id', $product->category_id) == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Unit Node --}}
                <div>
                    <label for="unit_id" class="block font-semibold text-slate-600 mb-1">Unit of Measurement</label>
                    <select name="unit_id" id="unit_id" class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition @error('unit_id') border-rose-500 @enderror">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->short_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Inventory Alert Level --}}
                <div>
                    <x-input name="stock_alert_quantity" type="number" label="Low Inventory Alert Level" placeholder="5" value="{{ old('stock_alert_quantity', $product->stock_alert_quantity) }}" step="0.01" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <p class="mt-1 text-[10px] text-slate-400">Trigger warnings when reserves hit this mark</p>
                </div>

                {{-- Visibility Status Checkboxes --}}
                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Active Visibility Flag</span>
                    </label>

                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_returnable" value="1" {{ old('is_returnable', $product->is_returnable) ? 'checked' : '' }} class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                        <span class="ml-2 font-medium text-slate-700">Allow Product Returns</span>
                    </label>
                </div>
            </div>

            {{-- Asset Media Cards Engine --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Asset Catalog Media</h2>

                {{-- Hero Thumbnail File Upload Field --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1.5">Product Image</label>
                    <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition relative">
                        <input type="file" name="image" id="image" accept="image/*" class="hidden">

                        <div id="imagePlaceholder" class="{{ $product->image ? 'hidden' : 'py-2' }}">
                            <i data-lucide="image" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                            <p class="text-slate-600 font-semibold mb-0.5">Click target window area to upload image</p>
                            <p class="text-[10px] text-slate-400">PNG, JPG, WEBP up to 2MB</p>
                        </div>

                        <div id="thumbnailPreview" class="{{ $product->image ? 'relative' : 'hidden' }}">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : '' }}" class="mx-auto h-24 object-cover rounded border border-slate-200">
                            <button type="button" id="removeThumbnail" class="absolute top-1 right-1 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow shadow-rose-200 transition">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>

                        <button type="button" onclick="document.getElementById('image').click()" class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition" id="thumbnailBtn">
                            {{ $product->image ? 'Change Image' : 'Choose File' }}
                        </button>
                    </div>
                    @error('image') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Form Operations Control Action Panel --}}
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm flex flex-col gap-2">
                <button type="submit" id="submitBtn" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm focus:outline-none">
                    <span id="btnText">Update Product</span>
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
                thumbnailBtn.textContent = 'Change Image';
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
            btnText.textContent = 'Update Product';
            btnSpinner.classList.add('hidden');
        });
    });
</script>
@endpush

@endsection
