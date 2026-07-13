@extends('admin.layouts.app')

@section('title', 'Point of Sale')

@section('content')
    <div class="pos-container h-screen flex flex-col" id="posSystem">
        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 px-4 py-3 shrink-0">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                    <i data-lucide="wallet" class="w-5 h-5 text-indigo-600"></i>
                    Point of Sale
                </h1>

                <div class="flex items-center gap-2">
                    @if($cashRegister)
                        @php
                            $data = $cashRegisterData ?? [];
                            $opening = $data['opening_amount'] ?? $cashRegister->opening_amount ?? 0;
                            $sales   = $data['sales_amount'] ?? 0;
                            $expense = $data['expense'] ?? 0;
                            $returns = $data['sales_returns'] ?? 0;
                            $closing = $opening + $sales - $expense - $returns;
                            $isClosed = $cashRegister->closed_at;
                        @endphp

                        <button
                            onclick="document.getElementById('closeRegisterModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold rounded-lg shadow-sm transition
                                {{ $isClosed
                                    ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60'
                                    : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60' }}">
                            <i data-lucide="{{ $isClosed ? 'rotate-cw' : 'circle-dollar-sign' }}" class="w-3.5 h-3.5"></i>
                            @if($isClosed)
                                Reopen ({{ money($cashRegister->closing_amount) }})
                            @else
                                Close ({{ money($closing) }})
                            @endif
                        </button>
                    @else
                        <div class="px-2.5 py-1 bg-amber-50 border border-amber-200/60 rounded-lg text-[11px] text-amber-700 font-medium">Register Not Opened</div>
                    @endif

                    <button id="clearCartBtn"
                        class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
                        <i data-lucide="refresh-ccw" class="w-3.5 h-3.5"></i>Clear Cart
                    </button>
                    <button id="draftOrdersBtn"
                        class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200/60 rounded-lg hover:bg-amber-100 transition">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>Draft
                    </button>
                    <button id="salesOrdersBtn"
                        class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/60 rounded-lg hover:bg-emerald-100 transition">
                        <i data-lucide="history" class="w-3.5 h-3.5"></i>Sales
                    </button>
                </div>
            </div>
        </div>

        <div id="ordersModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
            <div class="bg-white w-full max-w-2xl h-[80vh] rounded-xl shadow-xl flex flex-col">
                <div class="flex justify-between items-center px-4 py-3 border-b border-slate-200">
                    <h2 id="ordersModalTitle" class="text-sm font-bold uppercase tracking-wider text-slate-400">Orders</h2>
                    <button id="closeOrdersModal" class="text-slate-400 hover:text-rose-600 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div id="ordersList" class="flex-1 overflow-y-auto p-4 space-y-2 text-xs"></div>
                <div class="p-3 border-t border-slate-200 text-right">
                    <button onclick="closeModal()" class="h-9 px-3.5 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex overflow-hidden">
            <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
                <div class="bg-white border-b border-slate-200 p-3 shrink-0">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <i data-lucide="search" class="absolute w-3.5 h-3.5 left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" id="searchInput" placeholder="Search products by name..."
                                class="w-full pl-8 pr-3 h-9 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                        </div>

                        <div class="flex-1 relative">
                            <i data-lucide="scan-barcode" class="absolute w-3.5 h-3.5 left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" id="skuInput" placeholder="Scan or enter SKU..."
                                class="w-full pl-8 pr-3 h-9 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                        </div>

                        <select id="categoryFilter"
                            class="h-9 px-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="flex-1 overflow-y-auto p-4">
                    <div id="productsGrid"
                        class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-2">
                        @for($i = 0; $i < 20; $i++)
                            <div class="product-skeleton animate-pulse bg-white rounded-lg border border-gray-200 p-0">
                                <div class="h-24 bg-gray-200 rounded-t-lg"></div>
                                <div class="p-2 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div id="noProducts" class="hidden flex-col items-center justify-center py-16">
                        <div class="flex items-center justify-center w-14 h-14 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                            <i data-lucide="package-open" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">No products found</p>
                    </div>
                </div>
            </div>

            {{-- Cart Sidebar --}}
            <div class="w-full sm:w-96 lg:w-105 bg-white border-l border-slate-200 flex flex-col">
                {{-- Customer Info --}}
                <div class="p-3 border-b border-slate-200 bg-slate-50/70 relative">
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Customer</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div class="relative">
                            <input type="text" id="customerName" name="customer_name"
                                value="{{ request()->order_number ? $order->customer?->name : '' }}"
                                placeholder="Customer Name" autocomplete="off"
                                class="w-full h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <div id="customerNameDropdown"
                                class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                            </div>
                        </div>
                        <div class="relative">
                            <input type="text" id="customerPhone" name="customer_phone"
                                value="{{ request()->order_number ? $order->customer?->phone : '' }}"
                                placeholder="Phone Number" autocomplete="off"
                                class="w-full h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <div id="customerPhoneDropdown"
                                class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto p-3">
                    <h3 class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Cart Items (<span id="cartItemCount">0</span>)
                    </h3>

                    <div id="emptyCart" class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="flex items-center justify-center w-10 h-10 mb-2.5 border rounded-lg bg-slate-50 text-slate-400 border-slate-100">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Cart is empty</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Add products to continue</p>
                    </div>

                    <div id="cartItemsContainer" class="space-y-1.5"></div>
                </div>

                {{-- Cart Summary --}}
                <div class="border-t border-slate-200 p-3 bg-slate-50/70 text-xs">
                    {{-- Discount Section --}}
                    <div class="mb-3">
                        <div class="flex gap-1.5 mb-2">
                            <select id="discountType"
                                class="h-8 px-1.5 text-[11px] border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400">
                                <option value="fixed" {{ isset($order) && $order->discount_type == 'fixed' ? 'selected' : '' }}>৳ Fixed</option>
                                <option value="percent" {{ isset($order) && $order->discount_type == 'percent' ? 'selected' : '' }}>%</option>
                            </select>
                            <input type="number" id="discountInput" placeholder="Enter discount"
                                value="{{ isset($order) ? $order->discount_amount : '' }}"
                                class="flex-1 h-8 px-2 text-[11px] border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400">
                        </div>
                        <div id="discountApplied" class="hidden flex items-center justify-between text-emerald-600 text-[11px] font-semibold">
                            <span><i data-lucide="tag" class="w-3 h-3 mr-1 inline"></i>Discount Applied</span>
                            <span class="font-semibold">-৳<span id="discountAmount">0.00</span></span>
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-1 mb-3">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-semibold">৳<span id="subtotalAmount">0.00</span></span>
                        </div>
                        <div id="discountRow" class="hidden flex justify-between text-emerald-600">
                            <span>Discount</span>
                            <span class="font-semibold">-৳<span id="discountDisplay">0.00</span></span>
                        </div>
                        <div class="border-t border-slate-200 pt-1.5 flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-900">Total</span>
                            <span class="text-lg font-bold text-slate-900">৳<span id="totalAmount">0.00</span></span>
                        </div>
                    </div>

                    <div class="flex items-end gap-2 mb-2">
                        <div class="flex-1">
                            <div class="flex justify-between mb-0.5">
                                <label class="text-[10px] font-medium text-slate-500">Due</label>
                                <div class="text-[11px] font-bold text-rose-600">৳<span id="dueAmount">{{ isset($order) ? $order->due : 0 }}</span></div>
                            </div>
                            <div class="flex gap-1">
                                <input type="number" id="paidAmount" name="paid" min="0" step="0.01"
                                    value="{{ isset($order) ? $order->paid : '' }}"
                                    class="w-full h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                                    placeholder="Enter paid amount" />
                                <button type="button" id="fullPaidBtn"
                                    class="h-8 px-2.5 inline-flex items-center text-[11px] font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 whitespace-nowrap">
                                    Full
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 mb-2">
                        <div class="w-full">
                            <label class="block text-[10px] font-medium text-slate-500 mb-0.5">Cash Received</label>
                            <input type="number" id="cash_received" name="cash_received" min="0" step="0.01"
                                value="{{ isset($order) ? $order->cash_received : '' }}"
                                class="w-full h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-slate-400"
                                placeholder="Cash received" />
                        </div>
                        <div class="w-full">
                            <label class="block text-[10px] font-medium text-slate-500 mb-0.5">Cash Returned</label>
                            <input type="number" id="cash_returned" name="cash_returned" value="{{ isset($order) ? $order->cash_returned : 0 }}" readonly
                                class="w-full h-8 px-2 text-xs border border-slate-200 rounded-lg bg-slate-100" />
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <div class="grid grid-cols-5 gap-1">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="none" class="hidden peer payment_method" {{ !$order || (isset($order) && $order->payment_method->value==='none') ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[10px] peer-checked:border-slate-600 peer-checked:bg-slate-100 peer-checked:text-slate-700 transition">None</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" class="hidden peer payment_method" {{ isset($order) && $order->payment_method->value === 'cash' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[10px] peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition">Cash</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="card" class="hidden peer payment_method" {{ isset($order) && $order->payment_method->value === 'card' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[10px] peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 transition">Card</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bkash" class="hidden peer payment_method" {{ isset($order) && $order->payment_method->value === 'bkash' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[10px] peer-checked:border-pink-600 peer-checked:bg-pink-50 peer-checked:text-pink-600 transition font-semibold">bKash</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="nagad" class="hidden peer payment_method" {{ isset($order) && $order->payment_method->value === 'nagad' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[10px] peer-checked:border-orange-600 peer-checked:bg-orange-50 peer-checked:text-orange-600 transition">Nagad</div>
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <button id="updateOrderBtn"
                        class="{{ isset($order) ? '' : 'hidden' }} w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Update
                    </button>

                    <div class="grid grid-cols-2 gap-1.5 {{ isset($order) ? 'hidden' : '' }}">
                        <button id="holdOrderBtn" disabled
                            class="h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="pause" class="w-3.5 h-3.5"></i> Hold
                        </button>
                        <button id="completeOrderBtn" disabled
                            class="h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Complete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Variant Selection Modal --}}
        <div id="variantModal"
            class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-xl">
                <div class="px-4 py-3 border-b border-slate-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 id="modalProductName" class="text-sm font-bold text-slate-900"></h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Select size and color</p>
                        </div>
                        <button id="closeModalBtn" class="text-slate-400 hover:text-rose-600 transition">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <div id="modalContent">
                        <div class="mb-4">
                            <img id="modalProductImage" src="" alt="" class="w-full h-56 object-cover rounded-lg border border-slate-200">
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Available Variants</h3>
                            <div id="variantsList" class="grid gap-1.5"></div>

                            <div id="noVariantsMessage" class="hidden text-center py-6">
                                <p class="text-xs text-slate-500">No variants available for this product</p>
                                <button id="addWithoutVariantBtn"
                                    class="mt-3 h-9 px-4 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> Add to Cart - ৳<span id="noVariantPrice">0.00</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pos.partials.cash-register')

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="{{ asset('js/pos_cart.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                getProducts();

                const register = @json($cashRegister ?? null);

                const openModal  = document.getElementById('openRegisterModal');
                const closeModal = document.getElementById('closeRegisterModal');

                const openingInput = document.querySelector('[name="opening_amount"]');
                const closingInput = document.querySelector('[name="closing_amount"]');

                function showOpenModal() {
                    if (!openModal) return;
                    openModal.classList.remove('hidden');
                    setTimeout(() => openingInput?.focus(), 200);
                }

                function showCloseModal() {
                    if (!closeModal) return;
                    closeModal.classList.remove('hidden');
                    setTimeout(() => closingInput?.focus(), 200);
                }

                function hideCloseModal() {
                    closeModal?.classList.add('hidden');
                }

                function hideOpenModal() {
                    openModal?.classList.add('hidden');
                }

                // =========================
                // INITIAL STATE CONTROL
                // =========================
                if (!register) {
                    showOpenModal();

                    history.pushState({ modal: 'open' }, '');

                    window.onpopstate = function () {
                        showOpenModal();
                        history.pushState({ modal: 'open' }, '');
                    };
                }

                // =========================
                // CLOSE BUTTON EVENTS
                // =========================
                document.getElementById('closeCloseBtn')?.addEventListener('click', function () {
                    hideCloseModal();
                });

                document.getElementById('cancelCloseBtn')?.addEventListener('click', function () {
                    hideCloseModal();
                });

                // =========================
                // PREVENT REOPEN BUG ON SUBMIT
                // =========================
                document.querySelectorAll('form[action*="cashRegister.close"]').forEach(form => {
                    form.addEventListener('submit', function () {
                        hideCloseModal();
                        history.replaceState({}, document.title);
                    });
                });

            });

            function showProductsLoader() {
                let html = '';
                for (let i = 0; i < 20; i++) {
                    html += `
                        <div class="product-skeleton animate-pulse bg-white rounded-lg border border-slate-200 p-0">
                            <div class="h-24 bg-slate-200 rounded-t-lg"></div>
                            <div class="p-2 space-y-2">
                                <div class="h-3 bg-slate-200 rounded w-3/4"></div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                                    <div class="h-3 bg-slate-200 rounded w-1/4"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                $('#productsGrid').html(html);
            }

            function getProducts() {
                showProductsLoader();
                $.ajax({
                    url: "{{ route('admin.pos.getProducts') }}",
                    method: "GET",
                    success: function (response) {
                        $('#productsGrid').html(response.data.html);
                    },
                    error: function (xhr) {
                        console.error("Error fetching products: ", xhr);
                        $('#productsGrid').html('<p class="text-red-500 text-center py-4 col-span-full font-semibold">Failed to load products. Please try again.</p>');
                    }
                });
            }
        </script>
        <script>
            $(document).ready(function () {

                var selectedProduct = null;
                var paymentMethod = 'cash';
                const posOrdersUrl = "{{ route('admin.pos.loadOrders') }}";
                const orderId = "{{ $order->id ?? '' }}";

                // =========================
                // INIT
                // =========================
                function init() {
                    attachProductCardHandlers();
                }

                // =========================
                // PRODUCT CLICK
                // =========================
                function attachProductCardHandlers() {
                    $('#productsGrid').on('click', '.product-card', function () {
                        var productData = $(this).data('product');
                        if (productData) {
                            selectProduct(productData);
                        }
                    });
                }

                /* =========================
                    FILTER HELPERS
                ========================= */

                function matchesSearch(productData, query) {
                    if (!query) return true;
                    return productData.name.toLowerCase().includes(query);
                }

                function matchesCategory(productData, categoryId) {
                    if (!categoryId) return true;
                    return productData.category_id == categoryId;
                }

                /* =========================
                   MAIN FILTER FUNCTION
                ========================= */

                function filterProducts() {
                    var searchQuery = $('#searchInput').val().trim().toLowerCase();
                    var categoryId = $('#categoryFilter').val();

                    var visibleCount = 0;

                    $('.product-card').each(function () {
                        var productData = $(this).data('product');
                        if (!productData) return;

                        var matches =
                            matchesSearch(productData, searchQuery) &&
                            matchesCategory(productData, categoryId);

                        if (matches) {
                            $(this).show();
                            visibleCount++;
                        } else {
                            $(this).hide();
                        }
                    });

                    $('#noProducts')
                        .toggleClass('hidden', visibleCount !== 0)
                        .toggleClass('flex', visibleCount === 0);
                }

                let skuScanLock = false;
                let skuScanTimeout = null;

                $('#skuInput').on('input', function () {
                    let sku = $(this).val().trim().toLowerCase();

                    if (!sku) return;

                    // debounce (wait for scanner burst)
                    clearTimeout(skuScanTimeout);

                    skuScanTimeout = setTimeout(() => {

                        if (skuScanLock) return;

                        let foundProduct = null;
                        let foundVariant = null;

                        $('.product-card').each(function () {
                            let productData = $(this).data('product');
                            if (!productData) return;

                            // PRODUCT SKU MATCH
                            if (productData.sku &&
                                productData.sku.toLowerCase() === sku) {
                                foundProduct = productData;
                            }

                            // VARIANT SKU MATCH
                            if (productData.variants && productData.variants.length) {
                                productData.variants.forEach(v => {
                                    if (v.sku &&
                                        v.sku.toLowerCase() === sku) {
                                        foundProduct = productData;
                                        foundVariant = v;
                                    }
                                });
                            }
                        });

                        if (foundProduct) {
                            skuScanLock = true;

                            $('#skuInput').val('');

                            if (window.posCartManager) {
                                window.posCartManager.addToCart(
                                    foundProduct.id,
                                    foundVariant ? foundVariant.id : null,
                                    1
                                );
                            }

                            // unlock after short delay (prevents duplicate scans)
                            setTimeout(() => {
                                skuScanLock = false;
                            }, 500);
                        }

                    }, 150); // scanner-friendly delay
                });

                $('#searchInput').on('input', filterProducts);
                $('#categoryFilter').on('change', filterProducts);
                // $('#skuInput').on('change', handleSkuInput);

                // =========================
                // PRODUCT SELECT
                // =========================
                function selectProduct(product) {
                    if (product.variants && product.variants.length > 0) {
                        selectedProduct = product;
                        showVariantModal(product);
                    } else {
                        addToCartWithoutVariant(product);
                    }
                }

                // =========================
                // VARIANT MODAL
                // =========================
                function showVariantModal(product) {

                    $('#modalProductName').text(product.name);
                    $('#modalProductImage')
                        .attr('src', product.thumbnail)
                        .attr('alt', product.name);

                    var $variantsList = $('#variantsList');
                    $variantsList.empty();

                    if (product.variants.length > 0) {

                        $('#noVariantsMessage').addClass('hidden');

                        product.variants.forEach(function (variant) {

                            var disabled = variant.stock <= 0 ? 'disabled' : '';
                            var borderClass = variant.stock > 0 ? 'border-slate-200 hover:border-indigo-400' : 'border-rose-200 bg-rose-50';
                            var stockText = variant.stock > 0 ? `Stock: ${variant.stock}` : 'Out of Stock';
                            var stockClass = variant.stock > 0 ? 'text-emerald-600' : 'text-rose-600';

                            var btn = `
                                <button class="variant-btn flex items-center justify-between p-3 border rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed ${borderClass}"
                                    data-variant-id="${variant.id}" ${disabled}>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded border border-slate-300" style="background-color: ${variant.hex_code}"></div>
                                        <div class="text-left">
                                            <p class="font-semibold text-slate-800 text-xs">${variant.size_name} - ${variant.color_name}</p>
                                            <p class="text-[10px] text-slate-500">SKU: ${variant.sku}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-indigo-600">৳${parseFloat(variant.price).toFixed(2)}</p>
                                        <p class="text-[10px] ${stockClass}">${stockText}</p>
                                    </div>
                                </button>
                                `;

                            $variantsList.append(btn);
                        });

                        // click
                        $('.variant-btn').on('click', function () {
                            var variantId = $(this).data('variant-id');
                            var variant = product.variants.find(v => v.id == variantId);

                            if (variant) {
                                addToCart(product, variant);
                            }
                        });

                    } else {
                        $('#noVariantsMessage').removeClass('hidden');
                    }

                    $('#variantModal').removeClass('hidden');
                }

                // =========================
                // CLOSE MODAL
                // =========================

                $('#variantModal').on('click', function (e) {
                    if (e.target === this) {
                        closeVariantModal();
                    }
                });

               $('#closeModalBtn').on('click', function () {
                    closeVariantModal();
                });

                function closeVariantModal() {
                    $('#variantModal').addClass('hidden');
                    selectedProduct = null;
                }



                // =========================
                // Load Orders
                // =========================

                function openModal() {
                    $('#ordersModal').removeClass('hidden').addClass('flex');
                }

                function closeModal() {
                    $('#ordersModal').addClass('hidden').removeClass('flex');
                }

                // close button
                $(document).on('click', '#closeOrdersModal', function () {
                    closeModal();
                });

                // click outside to close
                $(document).on('click', '#ordersModal', function (e) {
                    if (e.target.id === 'ordersModal') {
                        closeModal();
                    }
                });

                function loadOrders(type) {
                    openModal();

                    $('#ordersList').html('<p>Loading...</p>');

                    $.ajax({
                        url: posOrdersUrl,
                        method: 'GET',
                        data: { type: type },
                        success: function (res) {
                            let html = '';

                            if (res.data.length === 0) {
                                html = '<p class="text-center text-gray-500">No orders found</p>';
                            } else {
                                let orderUrlTemplate = "{{ route('admin.orders.show', ':id') }}";
                                let posUrlTemplate = "{{ route('admin.pos.index') }}";

                                res.data.forEach(order => {
                                    let url = '';

                                    if (order.status === 'draft') {
                                        url = `${posUrlTemplate}?order_number=${order.order_number}`;
                                    } else {
                                        url = orderUrlTemplate.replace(':id', order.id);
                                    }

                                    html += `
                                        <div class="flex justify-between py-2.5 px-2 rounded hover:bg-slate-50 transition">
                                            <div>
                                                <p class="font-semibold text-xs">
                                                    <a href="${url}" class="text-indigo-600 hover:underline">#${order.order_number}</a>
                                                </p>
                                                <p class="text-[11px] text-slate-500">${order.customer_name}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-xs text-slate-800">${order.total}</p>
                                                <p class="text-[10px] text-slate-400">${order.status}</p>
                                            </div>
                                        </div>
                                    `;
                                });
                            }

                            $('#ordersList').html(html);
                        }
                    });
                }

                // buttons
                $(document).on('click', '#draftOrdersBtn', function () {
                    $('#ordersModalTitle').text('Draft Orders');
                    loadOrders('draft');
                });

                $(document).on('click', '#salesOrdersBtn', function () {
                    $('#ordersModalTitle').text('Today Sales');
                    loadOrders('sales');
                });

                // =========================
                // ADD TO CART (API ONLY)
                // =========================
                function addToCart(product, variant) {

                    if (window.posCartManager) {
                        window.posCartManager.addToCart(product.id, variant.id, 1);
                    }

                    $('#variantModal').addClass('hidden');
                    selectedProduct = null;
                }

                function addToCartWithoutVariant(product) {

                    if (window.posCartManager) {
                        window.posCartManager.addToCart(product.id, null, 1);
                    }

                    $('#variantModal').addClass('hidden');
                    selectedProduct = null;
                }

                $('#addWithoutVariantBtn').on('click', function () {
                    if (selectedProduct) {
                        addToCartWithoutVariant(selectedProduct);
                    }
                });

                // =========================
                // CLEAR CART
                // =========================
                document.addEventListener("click", (e) => {
                    if (e.target.id === "clearCartBtn") {

                        if (!confirm("Clear all items from cart?")) return;

                        if (window.posCartManager) {
                            window.posCartManager.clearCart();
                        }
                    }
                });

                // =========================
                // PAYMENT METHOD
                // =========================
                $('.payment-method-btn').on('click', function () {
                    $('.payment-method-btn')
                        .removeClass('bg-blue-600 text-white')
                        .addClass('bg-gray-200');

                    $(this)
                        .addClass('bg-blue-600 text-white');

                    paymentMethod = $(this).data('payment');
                });

                // Discount

                $('#discountInput, #discountType').on('input change', function () {
                    if (window.posCartManager) {
                        window.posCartManager.loadCart(window.posCartManager.orderNumber);
                    }
                });



                // =========================
                // COMPLETE ORDER (API CART)
                // =========================
                async function submitOrder(url, method = 'POST',shouldPrint = true) {

                    const urlParams = new URLSearchParams(window.location.search);
                    const orderNumber = urlParams.get('order_number');

                    const fetchURL = orderNumber ? `/admin/pos/cart?order_number=${orderNumber}` : `/admin/pos/cart`;

                    const res = await fetch(fetchURL);
                    const data = await res.json();

                    if (!data.success || !data.cart.items.length) {
                        alert('Cart is empty');
                        return;
                    }

                    const discount = parseFloat($("#discountDisplay").text()) || 0;
                    const paid = parseFloat($('#paidAmount').val()) || 0;
                    const totalAmount = parseFloat($("#totalAmount").text()) || 0;

                    const payload = {
                        customer_name: $('#customerName').val(),
                        customer_phone: $('#customerPhone').val(),
                        payment_method: $('input[name="payment_method"]:checked').val(),
                        cart_id : data.cart.id,
                        items: data.cart.items,
                        subtotal: data.cart.subtotal,
                        discount: discount,
                        total: (data.cart.total - discount),
                        paid: paid,
                        payable: totalAmount,
                        due: totalAmount - paid,
                        cash_received: $("#cash_received").val(),
                        cash_returned: $("#cash_returned").val()
                    };

                    $.ajax({
                        url: url,
                        method: method,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: JSON.stringify(payload),

                        success: function (res) {
                            if (res.success) {

                                window.showSuccess(res.message);

                                let receiptUrl = "{{ route('admin.pos.receipt', ':order_number') }}"
                                    .replace(':order_number', res.order_number);

                                resetPOS();

                                if (shouldPrint) {
                                    setTimeout(() => {
                                        printReceipt(receiptUrl, function () {
                                            window.location.href = "{{ route('admin.pos.index') }}";
                                        });
                                    }, 1500);

                                } else {
                                    window.location.href = "{{ route('admin.pos.index') }}";
                                }
                            }
                        },

                        error: function (xhr) {
                            let message = 'Something went wrong';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                if (xhr.responseJSON.errors) {
                                    let errors = Object.values(xhr.responseJSON.errors)
                                        .flat()
                                        .join('\n');

                                    message = errors;
                                }
                            }

                            window.showError(message);
                        }
                    });
                }

                $('#completeOrderBtn').on('click', function () {
                    submitOrder('{{ route("admin.pos.store") }}', 'POST',true);
                });

                $('#updateOrderBtn').on('click', function () {
                    submitOrder(`/admin/pos/update/${orderId}`, 'POST',true);
                });

                $('#holdOrderBtn').on('click', function () {
                    submitOrder('/admin/pos/draft', 'POST',false);
                });

                function resetPOS() {
                    window.posCartManager.clearCart();
                    $('#customerName').val('');
                    $('#customerPhone').val('');
                    $('#paidAmount').val('');
                    $('#discountInput').val('');
                    $('#dueAmount').text(0.00);
                    $("#cash_received").val('');
                    $("#cash_returned").val("0.00");
                    $('input[name="payment_method"][value=""]').prop('checked', true);
                }


                (function () {

                    let customerExists = false;
                    let selectedIndex = -1;
                    let currentList = [];
                    let isSelected = false; // 🔥 LOCK FLAG

                    // debounce helper
                    function debounce(fn, delay) {
                        let timer;
                        return function () {
                            clearTimeout(timer);
                            timer = setTimeout(() => fn.apply(this, arguments), delay);
                        };
                    }

                    function setupDropdown($input, $dropdown, type) {

                        const fetchCustomers = debounce(function () {

                            let val = $input.val().trim();

                            if (isSelected) return;

                            $dropdown.empty().addClass('hidden');
                            selectedIndex = -1;
                            currentList = [];

                            $('#customerId').val('');
                            customerExists = false;

                            if (val.length < 2) return;

                            $.ajax({
                                url: "{{ route('admin.pos.searchCustomers') }}",
                                data: { term: val },
                                dataType: 'json',
                                success: function (data) {

                                    if (!data.length) {
                                        $dropdown.addClass('hidden');
                                        return;
                                    }

                                    currentList = data;

                                    let html = '';

                                    data.forEach((c, i) => {

                                        let text = type === 'name'
                                            ? `${c.value} (${c.phone})`
                                            : `${c.phone} (${c.value})`;

                                        html += `
                                            <button type="button"
                                                class="dropdown-item text-start px-3 py-2 text-sm hover:bg-gray-100 w-100" data-index="${i}">
                                                ${text}
                                            </button>
                                            `;
                                    });

                                    $dropdown.html(html).removeClass('hidden');
                                }
                            });

                        }, 250);

                        // INPUT → unlock + search
                        $input.on('input', function () {
                            isSelected = false;
                            fetchCustomers();
                        });

                        // CLICK SELECT
                        $dropdown.on('click', '.dropdown-item', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            let index = $(this).data('index');
                            selectCustomer(index);
                            $('#customerNameDropdown').addClass('hidden').empty();
                            $('#customerPhoneDropdown').addClass('hidden').empty();
                        });

                        // KEYBOARD NAVIGATION
                        $input.on('keydown', function (e) {

                            let items = $dropdown.find('.dropdown-item');

                            if ($dropdown.hasClass('hidden') || !items.length) return;

                            if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                selectedIndex++;
                            }
                            else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                selectedIndex--;
                            }
                            else if (e.key === 'Enter') {
                                e.preventDefault();
                                if (selectedIndex >= 0) {
                                    selectCustomer(selectedIndex);
                                }
                                return;
                            }

                            if (selectedIndex >= items.length) selectedIndex = 0;
                            if (selectedIndex < 0) selectedIndex = items.length - 1;

                            items.removeClass('bg-gray-100');
                            items.eq(selectedIndex).addClass('bg-gray-100');
                        });
                    }

                    function selectCustomer(index) {

                        let c = currentList[index];

                        $('#customerName').val(c.value);
                        $('#customerPhone').val(c.phone);
                        $('#customerId').val(c.id);

                        customerExists = true;
                        isSelected = true;

                        $('#customerNameDropdown').addClass('hidden').empty();
                        $('#customerPhoneDropdown').addClass('hidden').empty();

                        currentList = [];
                        selectedIndex = -1;
                    }

                    // OUTSIDE CLICK
                    $(document).on('click', function (e) {

                        if (
                            $(e.target).closest('#customerName, #customerPhone').length === 0 &&
                            $(e.target).closest('#customerNameDropdown, #customerPhoneDropdown').length === 0
                        ) {
                            $('#customerNameDropdown').addClass('hidden').empty();
                            $('#customerPhoneDropdown').addClass('hidden').empty();
                        }
                    });

                    // INIT
                    setupDropdown($('#customerName'), $('#customerNameDropdown'), 'name');
                    setupDropdown($('#customerPhone'), $('#customerPhoneDropdown'), 'phone');

                })();

                // =========================
                // GET VALUES
                // =========================
                function getTotal() {
                    return parseFloat($("#totalAmount").text()) || 0;
                }

                function getPaid() {
                    return parseFloat($("#paidAmount").val()) || 0;
                }

                // =========================
                // UPDATE DUE CALCULATION
                // =========================
                function updateDue() {
                    let total = getTotal();
                    let paid = getPaid();

                    if (paid < 0) paid = 0;

                    if (paid > total) {
                        paid = total;
                        $("#paidAmount").val(total.toFixed(2));
                    }

                    let due = total - paid;

                    $("#dueAmount").text(due.toFixed(2));
                }

                // =========================
                // PAID INPUT EVENT
                // =========================
                $("#paidAmount").on("input", function () {
                    updateDue();
                });

                // =========================
                // FULL PAID BUTTON
                // =========================
                $("#fullPaidBtn").on("click", function () {
                    let total = getTotal();

                    $("#paidAmount").val(total.toFixed(2));
                    updateDue();
                });

                // =========================
                // SAFE INIT
                // =========================
                // updateDue();

                // =========================
                // EXTERNAL CALL (when cart/total changes)
                // =========================
                window.refreshPaymentUI = function () {
                    updateDue();
                };

                // =========================
                // GET PAYABLE (TOTAL)
                // =========================
                function getTotal() {
                    return parseFloat($("#totalAmount").text()) || 0;
                }

                // =========================
                // UPDATE CASH LOGIC
                // =========================
                function updateCash() {

                    let total = getTotal();
                    let cash = parseFloat($("#cash_received").val()) || 0;

                    if (cash < 0) cash = 0;

                    let returned = 0;

                    // CASE 1: Enough or extra cash
                    if (cash >= total) {
                        returned = cash - total;
                    } else {
                        returned = 0;
                    }

                    $("#cash_returned").val(returned.toFixed(2));
                }

                // =========================
                // INPUT EVENT
                // =========================
                $("#cash_received").on("input", function () {
                    updateCash();
                });

                // =========================
                // EXTERNAL REFRESH SUPPORT
                // =========================
                window.refreshCashUI = function () {
                    updateCash();
                };


                function printReceipt(url, callback) {
                    let printWindow = window.open(url, '_blank', 'width=800,height=600');

                    if (!printWindow) {
                        alert('Popup blocked! Please allow popups.');
                        return;
                    }

                    let isHandled = false;

                    printWindow.onload = function () {
                        printWindow.focus();

                        // trigger print
                        printWindow.print();
                    };

                    // Detect window close (works for cancel + print)
                    let checkClose = setInterval(function () {
                        if (printWindow.closed) {
                            clearInterval(checkClose);

                            if (!isHandled) {
                                isHandled = true;
                                if (callback) callback();
                            }
                        }
                    }, 500);

                    // Fallback: afterprint (not reliable alone, but useful)
                    printWindow.onafterprint = function () {
                        if (!isHandled) {
                            isHandled = true;

                            printWindow.close();

                            if (callback) callback();
                        }
                    };
                }

                // =========================
                // INIT CALL
                // =========================
                init();
            });
        </script>
    @endpush

    <style>
        .pos-container {
            max-height: calc(100vh - 64px);
        }

        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Line clamp */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Product card hover enhancements */
        .product-card {
            min-height: 140px;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        .product-card:active {
            transform: translateY(0);
        }
    </style>
@endsection
