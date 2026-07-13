@php
    $hasProducts = $products->count() > 0;
@endphp

@if($hasProducts)
    @foreach($products as $product)
        @php
            $stock = $product->currentStock;
            $imageSrc = $product->thumbnail
                ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : asset('storage/' . $product->thumbnail))
                : asset('assets/images/default.png');

            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
                'stock' => $stock,
                'currentStock' => $stock,
                'stock_in' => $stock,
                'thumbnail' => $product->thumbnail,
                'category_id' => $product->category_id,
                'has_variants' => false,
                'variants' => [],
            ];
        @endphp

        <div
            class="product-card bg-white rounded-lg border border-slate-200 hover:border-indigo-400 hover:shadow-md transition cursor-pointer group"
            data-product-id="{{ $product->id }}"
            data-product='@json($productData)'>

            <div class="h-24 bg-slate-100 rounded-t-lg overflow-hidden relative">
                <img src="{{ $imageSrc }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition"
                    loading="lazy">

                @if($stock <= 0)
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center rounded-t-lg">
                        <span class="bg-rose-500 text-white px-2 py-0.5 rounded text-[10px] font-semibold">Out of Stock</span>
                    </div>
                @endif
            </div>

            <div class="p-2">
                <h3 class="text-xs font-semibold text-slate-900 mb-1 line-clamp-2 leading-tight">{{ $product->name }}</h3>
                <div class="flex items-center justify-between gap-1">
                    <span class="text-sm font-bold text-indigo-600">৳{{ number_format($product->price, 2) }}</span>
                    <span class="text-[10px] text-slate-500">Stock: {{ $stock }}</span>
                </div>
            </div>
        </div>
    @endforeach
@endif

<div id="noProducts"
    class="{{ $hasProducts ? 'hidden' : 'flex' }} flex-col items-center justify-center py-12 text-slate-500 col-span-full">
    <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
    </svg>
    <p class="text-lg font-medium text-slate-500">No Products Found</p>
    <p class="text-sm text-slate-400 mt-1">No products are currently available.</p>
</div>
