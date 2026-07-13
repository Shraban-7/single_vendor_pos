@extends('admin.layouts.app')
@section('title', 'Manage Stock')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Manage Stock</h1>
        <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $product->name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.stock-history', $product) }}"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100/60 transition">
            <i data-lucide="history" class="w-3.5 h-3.5"></i>
            <span>Stock History</span>
        </a>
        <a href="{{ route('admin.products.edit', $product) }}"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Edit</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Product Base Stock (Without Variants) --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex flex-col justify-between">
        <div class="flex items-start justify-between border-b border-slate-100 pb-3 mb-4">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Product Stock Base</h2>
                <p class="text-[11px] font-mono text-slate-500 mt-0.5">{{ $product->sku ?? 'No SKU Assigned' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Current Balance</p>
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5" id="product-stock-{{ $product->id }}">{{ $product->currentStock }}</p>
            </div>
        </div>

        <form class="stock-form space-y-3.5 text-xs" data-product-id="{{ $product->id }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Action Toggle System --}}
            <div>
                <label class="block font-semibold text-slate-600 mb-1.5">Transaction Action Pipeline</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="relative flex items-center justify-center px-3 py-2 border border-slate-200 bg-slate-50/50 rounded-lg cursor-pointer transition select-none hover:bg-slate-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 group">
                        <input type="radio" name="action_type" value="add" class="sr-only" checked>
                        <div class="flex items-center gap-1.5 font-semibold text-slate-600 group-has-[:checked]:text-emerald-700">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Add Stock</span>
                        </div>
                    </label>
                    <label class="relative flex items-center justify-center px-3 py-2 border border-slate-200 bg-slate-50/50 rounded-lg cursor-pointer transition select-none hover:bg-slate-50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 group">
                        <input type="radio" name="action_type" value="remove" class="sr-only">
                        <div class="flex items-center gap-1.5 font-semibold text-slate-600 group-has-[:checked]:text-rose-700">
                            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                            <span>Remove Stock</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Quantity --}}
            <div>
                <x-input name="quantity" label="Adjustment Quantity *" type="number" min="1" required placeholder="Enter unit quantity increment" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
            </div>

            {{-- Note Transaction --}}
            <div>
                <x-textarea name="note" label="Transaction Audit Note" rows="2" placeholder="Add custom ledger notes for reference..." class="text-xs bg-slate-50/50 focus:bg-white" />
            </div>

            {{-- Submit Operation --}}
            <button type="submit" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm focus:outline-none">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                <span>Update Stock Parameters</span>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.stock-form');

        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin mr-1"></i><span>Processing...</span>';
                if(window.lucide) lucide.createIcons();

                try {
                    const formData = new FormData(form);
                    const actionType = formData.get('action_type');
                    const route = actionType === 'add' ?
                        '{{ route("admin.products.stock.add") }}' :
                        '{{ route("admin.products.stock.remove") }}';

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
                        const productId = formData.get('product_id');

                        if (productId) {
                            const stockDisplay = document.getElementById('product-stock-' + productId);
                            if (stockDisplay) stockDisplay.textContent = data.stock_after;
                        }

                        form.reset();
                        form.querySelector('input[name="action_type"][value="add"]').checked = true;

                        if (window.showToast) showToast('success', data.message);
                    } else {
                        if (window.showToast) showToast('error', data.message || 'An error occurred');
                    }
                } catch (error) {
                    if (window.showToast) showToast('error', 'An error occurred. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    if(window.lucide) lucide.createIcons();
                }
            });
        });
    });
</script>
@endpush

@endsection
