@extends('admin.layouts.app')
@section('title', 'Manage Stock')
@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Manage Stock</h1>
            <p class="text-gray-500">{{ $product->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.stock-history', $product) }}"
                class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-history mr-2"></i>Stock History
            </a>
            <a href="{{ route('admin.products.edit', $product) }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Edit
            </a>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Product Base Stock --}}
    @if($product->variants->isEmpty())
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Product Stock</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $product->sku ?? 'No SKU' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Current Stock</p>
                <p class="text-3xl font-bold text-gray-900" id="product-stock-{{ $product->id }}">{{ $product->currentStock }}</p>
            </div>
        </div>

        <form class="stock-form" data-product-id="{{ $product->id }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Action Type Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Action</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg cursor-pointer transition hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="action_type" value="add" class="sr-only peer" checked>
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700 peer-checked:text-green-700">
                            <i class="fas fa-plus"></i>
                            <span>Add Stock</span>
                        </div>
                    </label>
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg cursor-pointer transition hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="radio" name="action_type" value="remove" class="sr-only peer">
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700 peer-checked:text-red-700">
                            <i class="fas fa-minus"></i>
                            <span>Remove Stock</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Quantity Input --}}
            <div class="mb-4">
                <x-input name="quantity" label="Quantity" type="number" min="1" required placeholder="Enter quantity" />
            </div>

            {{-- Note Input --}}
            <div class="mb-4">
                <x-textarea name="note" label="Note(optional)" rows="3" placeholder="Add a note for this transaction..." />
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check mr-2"></i>Update Stock
            </button>
        </form>
    </div>
    @endif

    {{-- Product Variants Stock --}}
    @foreach($product->variants as $variant)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $variant->variant_name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $variant->sku ?? 'No SKU' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Current Stock</p>
                <p class="text-3xl font-bold text-gray-900" id="variant-stock-{{ $variant->id }}">{{ $variant->currentStock }}</p>
            </div>
        </div>

        <form class="stock-form" data-variant-id="{{ $variant->id }}">
            @csrf
            <input type="hidden" name="variant_id" value="{{ $variant->id }}">

            {{-- Action Type Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Action</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg cursor-pointer transition hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="action_type" value="add" class="sr-only peer" checked>
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700 peer-checked:text-green-700">
                            <i class="fas fa-plus"></i>
                            <span>Add Stock</span>
                        </div>
                    </label>
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg cursor-pointer transition hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="radio" name="action_type" value="remove" class="sr-only peer">
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700 peer-checked:text-red-700">
                            <i class="fas fa-minus"></i>
                            <span>Remove Stock</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Quantity Input --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number"
                    name="quantity"
                    min="1"
                    required
                    placeholder="Enter quantity"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Note Input --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Note <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea name="note"
                    rows="2"
                    placeholder="Add a note for this transaction..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check mr-2"></i>Update Stock
            </button>
        </form>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.stock-form');

        forms.forEach(form => {
            const quantityInput = form.querySelector('input[name="quantity"]');
            const actionRadios = form.querySelectorAll('input[name="action_type"]');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                try {
                    const formData = new FormData(form);
                    const actionType = formData.get('action_type');
                    const route = actionType === 'add' ?
                        '{{ route("admin.products.stock.add") }}' :
                        '{{ route("admin.products.stock.remove") }}';

                    // Remove action_type from form data as it's not needed by backend
                    formData.delete('action_type');

                    const response = await fetch(route, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update stock display
                        const productId = formData.get('product_id');
                        const variantId = formData.get('variant_id');

                        if (variantId) {
                            const stockDisplay = document.getElementById('variant-stock-' + variantId);
                            if (stockDisplay) {
                                stockDisplay.textContent = data.stock_after;
                            }

                        } else if (productId) {
                            const stockDisplay = document.getElementById('product-stock-' + productId);
                            if (stockDisplay) {
                                stockDisplay.textContent = data.stock_after;
                            }



                        }

                        // Reset form
                        form.reset();
                        // Reset to "add" action
                        form.querySelector('input[name="action_type"][value="add"]').checked = true;


                        // Show success message
                        showToast('success', data.message);
                    } else {
                        showToast('error', data.message || 'An error occurred');
                    }
                } catch (error) {
                    showToast('error', 'An error occurred. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    });
</script>
@endpush

@endsection