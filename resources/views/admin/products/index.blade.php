@extends('admin.layouts.app')
@section('title', 'Products')
@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Products</h1>
            <p class="text-slate-500">Manage your product catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition shadow-sm">
            <i data-lucide="plus"  class="w-4 h-4 fill-current mr-2"></i>Add Product
        </a>
    </div>
</div>

{{-- Filters & Search --}}
<div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm mb-6">
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
        <div class="grid md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-slate-700 mb-2">Search Products</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        placeholder="Search by name, SKU, or brand...">
                    <i data-lucide="search"  class="w-4 h-4 fill-current absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>

            {{-- Category Filter --}}
            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                <select name="category" id="category"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" id="status"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
            {{-- Min Price --}}
            <div>
                <label for="min_price" class="block text-sm font-medium text-slate-700 mb-2">Min Price (৳)</label>
                <input type="number" step="0.01" name="min_price" id="min_price" value="{{ request('min_price') }}"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="0.00">
            </div>

            {{-- Max Price --}}
            <div>
                <label for="max_price" class="block text-sm font-medium text-slate-700 mb-2">Max Price (৳)</label>
                <input type="number" step="0.01" name="max_price" id="max_price" value="{{ request('max_price') }}"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="0.00">
            </div>

            {{-- Sort --}}
            <div>
                <label for="sort" class="block text-sm font-medium text-slate-700 mb-2">Sort By</label>
                <select name="sort" id="sort"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i data-lucide="filter"  class="w-4 h-4 fill-current mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition">
                    <i data-lucide="rotate-cw"  class="w-4 h-4 fill-current"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Products Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($products as $product)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->thumbnail }}"
                                alt="image"
                                class="w-12 h-12 rounded-lg object-cover bg-slate-100">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900">{{ $product->name }}</p>
                                @if($product->brand)
                                <p class="text-xs text-slate-500">{{ $product->brand }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-900">{{ $product->sku ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-900">{{ $product->category?->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ money($product->price) }}</p>
                            @if($product->compare_price && $product->compare_price > $product->price)
                            <p class="text-xs text-slate-500 line-through">{{ money($product->compare_price) }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->stock_in <= 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                            <i data-lucide="x-circle"  class="w-4 h-4 fill-current mr-1"></i> Out of Stock
                            </span>
                            @elseif($product->stock_in <= $product->low_stock_threshold)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <i data-lucide="alert-triangle"  class="w-4 h-4 fill-current mr-1"></i> Low ({{ $product->stock_in }})
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <i data-lucide="check-circle"  class="w-4 h-4 fill-current mr-1"></i> In Stock ({{ $product->stock_in }})
                                </span>
                                @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            @if($product->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i data-lucide="eye"  class="w-4 h-4 fill-current mr-1"></i> Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                <i data-lucide="eye-off"  class="w-4 h-4 fill-current mr-1"></i> Inactive
                            </span>
                            @endif
                            @if($product->is_featured)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i data-lucide="star"  class="w-4 h-4 fill-current mr-1"></i> Featured
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="#" class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View">
                                <i data-lucide="eye"  class="w-4 h-4 fill-current"></i>
                            </a>
                            <a href="{{ route('admin.products.manage-stock', $product) }}" class="p-2 text-slate-600 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition" title="Manage Stock">
                                <i data-lucide="boxes"  class="w-4 h-4 fill-current"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Edit">
                                <i data-lucide="pencil"  class="w-4 h-4 fill-current"></i>
                            </a>
                            <button onclick="confirmDelete({{ $product->id }})" class="p-2 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete">
                                <i data-lucide="trash-2"  class="w-4 h-4 fill-current"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="package-open"  class="w-16 h-16 fill-current text-6xl text-slate-300 mb-4"></i>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">No Products Found</h3>
                            <p class="text-slate-500 mb-4">Start by adding your first product to the catalog.</p>
                            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                                <i data-lucide="plus"  class="w-4 h-4 fill-current mr-2"></i>Add Product
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-700">
                Showing <span class="font-medium">{{ $products->firstItem() }}</span> to <span class="font-medium">{{ $products->lastItem() }}</span> of <span class="font-medium">{{ $products->total() }}</span> results
            </div>
            <div>
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
            // Create a form and submit it
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

    // Auto-submit form on filter change
    document.querySelectorAll('#category, #status, #sort').forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush

@endsection