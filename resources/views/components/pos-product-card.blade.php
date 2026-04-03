@props(['product'])

<?php
$stockBadge = '';
if (($product->stock_in ?? 0) <= 0) {
    $stockBadge = '<div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center rounded"><span class="bg-red-500 text-white px-2 py-0.5 rounded text-[10px] font-semibold">Out of Stock</span></div>';
}

$imageSrc = $product->image ? asset('storage/' . $product->image) : asset('assets/images/default.png');
$price = number_format($product->price, 2);
$stock = $product->stock_in ?? 0;

// Check if product has variants
$variantCount = $product->variants ? count($product->variants) : 0;

$variantsData = [];
foreach ($product->variants as $variant) {
    $variantsData[] = [
        'id' => $variant->id,
        'sku' => $variant->sku,
        'stock' => $variant->currentStock,
        'price' => $variant->price ?? $product->price,
        'size_name' => $variant->size->name ?? null,
        'color_name' => $variant->color->name ?? null,
        'hex_code' => $variant->color->hex_code ?? '#ccc',
    ];
}

$productData = [
    'id' => $product->id,
    'name' => $product->name,
    'sku' => $product->sku,
    'price' => $product->price,
    'compare_price' => $product->compare_price,
    'stock' => $product->currentStock,
    'thumbnail' => $product->thumbnail,
    'variants' => $variantsData
];
?>

<div
    class="product-card bg-white rounded-lg border border-gray-200 hover:border-blue-500 hover:shadow-md transition cursor-pointer group"
    data-product-id="{{ $product->id }}"
    data-product='@json($productData)'>

    <div class="h-24 bg-gray-100 rounded-t-lg overflow-hidden relative">
        <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">

        @if($stock <= 0)
            <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center rounded">
            <span class="bg-red-500 text-white px-2 py-0.5 rounded text-[10px] font-semibold">Out of Stock</span>
    </div>
    @endif

    @if($variantCount > 0)
    <span class="absolute top-1 right-1 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
        {{ $variantCount }} variant{{ $variantCount > 1 ? 's' : '' }}
    </span>
    @endif
</div>

<div class="p-2">
    <h3 class="text-xs font-semibold text-gray-900 mb-1 line-clamp-2 leading-tight">{{ $product->name }}</h3>
    <div class="flex items-center justify-between gap-1">
        <span class="text-sm font-bold text-blue-600">৳{{ $price }}</span>
        <span class="text-[10px] text-gray-500">Stock: {{ $stock }}</span>
    </div>
</div>
</div>