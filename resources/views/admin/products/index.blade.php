@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Products</h1>
        <p class="text-xs text-slate-500">Manage, update, and track your active product catalog inventory.</p>
    </div>
    <div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Product</span>
        </a>
    </div>
</div>

{{-- Compact Filter Framework --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-2.5">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

            {{-- Search Input --}}
            <div class="sm:col-span-3">
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search name, SKU, brand..."
                        class="w-full pl-8 pr-3 text-xs transition border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                    <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                </div>
            </div>

            {{-- Category --}}
            <div class="sm:col-span-2">
                <select name="category" id="category" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="sm:col-span-2">
                <select name="status" id="status" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            {{-- Price Parameters --}}
            <div class="sm:col-span-1.5">
                <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" placeholder="Min ৳" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none bg-slate-50/50">
            </div>
            <div class="sm:col-span-1.5">
                <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" placeholder="Max ৳" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none bg-slate-50/50">
            </div>

            {{-- Sorting --}}
            <div class="sm:col-span-1">
                <select name="sort" id="sort" class="w-full px-1.5 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none bg-slate-50/50" title="Sort Order">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price ↑</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price ↓</option>
                </select>
            </div>

            {{-- Action Controls --}}
            <div class="flex gap-1 sm:col-span-1">
                <button type="submit" class="flex items-center justify-center flex-1 text-xs text-white transition rounded-lg shadow-sm h-9 bg-slate-800 hover:bg-slate-900" title="Filter Selection">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                </button>
                <a href="{{ route('admin.products.index') }}" class="flex items-center justify-center transition bg-white border rounded-lg shadow-sm w-9 h-9 text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-800" title="Clear Filters">
                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>
    </form>
</div>

{{-- Data Dense Products Grid Table --}}
<div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Product Catalog Item</th>
                    <th class="px-4 py-3">SKU</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Price Metric</th>
                    <th class="px-4 py-3">Stock Count</th>
                    <th class="px-4 py-3">Visibility</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($products as $product)
                    <tr class="transition-colors hover:bg-slate-50/60">
                        {{-- Identity Meta --}}
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $product->thumbnail }}" alt="thumb" class="flex-shrink-0 object-cover border rounded-md w-9 h-9 border-slate-100 bg-slate-50">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm block truncate max-w-[220px]">{{ $product->name }}</span>
                                    @if($product->brand)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ $product->brand }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        {{-- SKU --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                            {{ $product->sku ?? '—' }}
                        </td>
                        {{-- Category label --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $product->category?->name ?? 'Unassigned' }}
                        </td>
                        {{-- Price parameters --}}
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="block text-sm font-bold text-slate-900">{{ money($product->price) }}</span>
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-[10px] text-slate-400 line-through block mt-0.5">{{ money($product->compare_price) }}</span>
                            @endif
                        </td>
                        {{-- Dynamic Stock Metrics --}}
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @if($product->stock_in <= 0)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-700 border border-rose-100/70">Out of Stock</span>
                            @elseif($product->stock_in <= $product->low_stock_threshold)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 border border-amber-200/60">Low ({{ $product->stock_in }})</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">In Stock ({{ $product->stock_in }})</span>
                            @endif
                        </td>
                        {{-- Functional Visibility Toggles --}}
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                @if($product->is_active)
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-slate-900 text-white">Active</span>
                                @else
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200/80">Inactive</span>
                                @endif

                                @if($product->is_featured)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">★ Star</span>
                                @endif
                            </div>
                        </td>
                        {{-- Actions strip --}}
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-0.5">
                                <a href="{{ route('admin.products.manage-stock', $product) }}" class="p-1 transition rounded text-slate-400 hover:text-violet-600 hover:bg-slate-100" title="Inventory Matrix">
                                    <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="p-1 transition rounded text-slate-400 hover:text-emerald-600 hover:bg-slate-100" title="Edit Catalog Entry">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <button onclick="confirmDelete({{ $product->id }})" class="p-1 transition rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100" title="Delete Product">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-slate-500">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                    <i data-lucide="package-open" class="w-5 h-5"></i>
                                </div>
                                <h3 class="font-bold text-slate-900">No matching products found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Try widening your search terms or modification ranges.</p>
                                <a href="{{ route('admin.products.index') }}" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:underline">Reset Catalog View</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Clean Footer Pagination Component --}}
    @if($products->hasPages())
        <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
            <div class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-slate-600">
                <div>
                    Showing <span class="font-semibold text-slate-800">{{ $products->firstItem() }}</span> to <span class="font-semibold text-slate-800">{{ $products->lastItem() }}</span> of <span class="font-semibold text-slate-800">{{ $products->total() }}</span> variants
                </div>
                <div class="font-medium">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function confirmDelete(productId) {
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${productId}/delete`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Auto-submit form pipeline on select configuration updates
    document.querySelectorAll('#category, #status, #sort').forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush

@endsection
