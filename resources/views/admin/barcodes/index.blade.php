@extends('admin.layouts.app')

@section('title', 'Print Barcode')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Print Barcode</h1>
        <p class="text-xs text-slate-500">Generate, evaluate, and batch-print thermal barcode labels for inventory lines.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[420px_minmax(0,1fr)] gap-4 items-start">

    {{-- Left Control Console Panel --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Product Line Configuration</h2>
        </div>

        <div class="p-4">
            <form id="productForm" class="space-y-3.5 text-xs">

                {{-- Product Select --}}
                <div>
                    <label for="product" class="block font-semibold text-slate-600 mb-1">Select Base Product</label>
                    <select name="product_id" id="product" required
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                        <option value="" disabled selected>Choose a catalog item...</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->price }}"
                                    data-stock="{{ $product->currentStock }}"
                                    data-sku="{{ $product->sku }}">
                                {{ $product->name }} | SKU: {{ $product->sku ?? 'N/A' }} | Balance: {{ $product->currentStock }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Product Readonly Details Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Product Title</label>
                        <input type="text" id="name" readonly
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 rounded-lg bg-slate-50/50 text-slate-700 font-medium focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Stock Keeping Unit (SKU)</label>
                        <input type="text" id="sku" readonly
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 rounded-lg bg-slate-50/50 text-slate-700 font-mono focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Retail Valuation</label>
                        <input type="text" id="price" readonly
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 rounded-lg bg-slate-50/50 text-slate-700 font-semibold focus:outline-none">
                    </div>
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="qty" class="block font-semibold text-slate-600 mb-1">Print Run Volume (Labels Quantity)</label>
                    <input type="number" id="qty" name="quantity" min="1" value="1" required
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                </div>

                {{-- Action Group --}}
                <div class="flex flex-col sm:flex-row gap-2 pt-1">
                    <button type="button" id="generate"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition focus:outline-none">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Preview Labels</span>
                    </button>
                    <button type="button" id="printBtn" disabled
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg shadow-sm hover:bg-slate-900 transition opacity-50 cursor-not-allowed focus:outline-none">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Dispatch Print</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Right Visual Output Preview Panel --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[65vh]">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/40 flex items-center justify-between shrink-0">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Thermal Output Preview Canvas</h2>
            <span id="labelCount" class="text-[10px] text-slate-400 font-mono font-medium"></span>
        </div>

        <div id="labelsContainer" class="flex-1 overflow-y-auto p-4 bg-slate-50/30">
            {{-- Blank Matrix Illustration State --}}
            <div class="flex flex-col items-center justify-center h-full text-center" id="emptyState">
                <i data-lucide="scan-line" class="w-8 h-8 text-slate-300 mb-2"></i>
                <h3 class="text-xs font-bold text-slate-700 mb-0.5">No Layout Previews Mounted</h3>
                <p class="text-[11px] text-slate-400">Configure parameters on the left node to synthesize render bars.</p>
            </div>

            <div id="labelsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 hidden"></div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const productSelect = document.getElementById('product');
        const nameInput = document.getElementById('name');
        const skuInput = document.getElementById('sku');
        const priceInput = document.getElementById('price');
        const qtyInput = document.getElementById('qty');
        const labelsContainer = document.getElementById('labelsContainer');
        const labelsGrid = document.getElementById('labelsGrid');
        const emptyState = document.getElementById('emptyState');
        const labelCount = document.getElementById('labelCount');
        const printBtn = document.getElementById('printBtn');
        const siteName = "{{ $siteName }}";

        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;

            nameInput.value = selectedOption.dataset.name || '';
            skuInput.value = selectedOption.dataset.sku || '';
            priceInput.value = selectedOption.dataset.price ? '৳ ' + selectedOption.dataset.price : '';
        });

        document.getElementById('generate').addEventListener('click', function() {
            const qty = Math.max(1, parseInt(qtyInput.value) || 1);
            const sku = skuInput.value;
            const name = nameInput.value;
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = selectedOption ? selectedOption.dataset.price : '';

            if (!sku) {
                if (window.showToast) window.showToast('error', 'Please define product parameters first.');
                return;
            }

            labelsGrid.innerHTML = '';
            emptyState.classList.add('hidden');
            labelsGrid.classList.remove('hidden');

            for (let i = 0; i < qty; i++) {
                const wrap = document.createElement('div');
                wrap.className = 'bg-white border border-slate-200 rounded-xl p-3 text-center shadow-xs transition-colors hover:border-slate-300';

                wrap.innerHTML = `
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">${siteName}</div>
                    <div class="text-xs font-semibold text-slate-800 truncate px-1">${name}</div>
                    <div class="flex justify-center py-1 mt-0.5">
                        <svg class="barcode mx-auto"
                            jsbarcode-value="${sku}"
                            jsbarcode-width="1.3"
                            jsbarcode-height="32"
                            jsbarcode-fontSize="9"
                            jsbarcode-margin="0">
                        </svg>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 mt-1.5 pt-1.5 text-[10px]">
                        <span class="text-slate-400 font-mono">SKU: ${sku}</span>
                        <span class="font-bold text-indigo-600 font-mono">৳ ${price}</span>
                    </div>
                `;

                labelsGrid.appendChild(wrap);
            }

            JsBarcode(".barcode").init();

            labelCount.textContent = `[ ${qty} ${qty > 1 ? 'Labels' : 'Label'} Rendered ]`;
            printBtn.disabled = false;
            printBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        });

        printBtn.addEventListener('click', function() {
            const quantity = qtyInput.value;
            if (!quantity || quantity <= 0) return;

            const url = "{{ route('admin.barcodes.label') }}" +
                "?sku=" + skuInput.value +
                "&quantity=" + quantity;

            window.open(url, '_blank');
            resetForm();
        });

        function resetForm() {
            labelsGrid.innerHTML = '';
            labelsGrid.classList.add('hidden');
            emptyState.classList.remove('hidden');
            labelCount.textContent = '';
            printBtn.disabled = true;
            printBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('productForm').reset();
            productSelect.selectedIndex = 0;
            nameInput.value = '';
            skuInput.value = '';
            priceInput.value = '';
        }
    });
</script>
@endpush

<style>
    #labelsContainer::-webkit-scrollbar {
        width: 4px;
    }
    #labelsContainer::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 99px;
    }
    #labelsContainer::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }
    #labelsContainer::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

@endsection
