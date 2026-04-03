@extends('admin.layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="pos-container h-screen flex flex-col" id="posSystem">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex-shrink-0">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cash-register text-blue-600"></i>
                Point of Sale
            </h1>

            <div class="flex items-center gap-3">
                <button id="clearCartBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fas fa-refresh mr-2"></i>New Sale
                </button>
                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fas fa-history mr-2"></i>Orders
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- Products Section --}}
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            {{-- Search and Filters --}}
            <div class="bg-white border-b border-gray-200 p-4 flex-shrink-0">
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Search --}}
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search products by name or SKU..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Category Filter --}}
                    <select id="categoryFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    {{-- Barcode Scanner Button --}}
                    <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                        <i class="fas fa-barcode mr-2"></i>Scanner
                    </button>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="flex-1 overflow-y-auto p-4">
                <div id="productsGrid" class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-2">
                    @foreach($products as $product)
                        <x-pos-product-card :product="$product" />
                    @endforeach
                </div>

                {{-- No Products Found --}}
                <div id="noProducts" class="hidden flex-col items-center justify-center py-16">
                    <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No products found</p>
                </div>
            </div>
        </div>

        {{-- Cart Sidebar --}}
        <div class="w-full sm:w-96 lg:w-[420px] bg-white border-l border-gray-200 flex flex-col">
            {{-- Customer Info --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Customer</label>
                <input
                    type="text"
                    id="customerName"
                    placeholder="Walk-in Customer"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Cart Items (<span id="cartCount">0</span>)</h3>

                <div id="emptyCart" class="flex flex-col items-center justify-center py-16 text-center">
                    <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-500">Cart is empty</p>
                    <p class="text-gray-400 text-sm mt-1">Add products to continue</p>
                </div>

                <div id="cartItems" class="space-y-2">
                    {{-- Cart items will be rendered here by jQuery --}}
                </div>
            </div>

            {{-- Cart Summary --}}
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                {{-- Discount Section --}}
                <div class="mb-4">
                    <div class="flex gap-2 mb-3">
                        <input
                            type="text"
                            id="discountCode"
                            placeholder="Discount code or %"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <button id="applyDiscountBtn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium">
                            Apply
                        </button>
                    </div>

                    <div id="discountApplied" class="hidden flex items-center justify-between text-sm text-green-600">
                        <span><i class="fas fa-tag mr-1"></i>Discount Applied</span>
                        <span class="font-semibold">-৳<span id="discountAmount">0.00</span></span>
                    </div>
                </div>

                {{-- Totals --}}
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">৳<span id="subtotalAmount">0.00</span></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tax (0%)</span>
                        <span class="font-semibold">৳0.00</span>
                    </div>
                    <div id="discountRow" class="hidden flex justify-between text-sm text-green-600">
                        <span>Discount</span>
                        <span class="font-semibold">-৳<span id="discountDisplay">0.00</span></span>
                    </div>
                    <div class="border-t border-gray-300 pt-2 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-blue-600">৳<span id="totalAmount">0.00</span></span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            data-payment="cash"
                            class="payment-method-btn bg-blue-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:opacity-90 transition">
                            <i class="fas fa-money-bill-wave"></i> Cash
                        </button>
                        <button
                            data-payment="card"
                            class="payment-method-btn bg-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:opacity-90 transition">
                            <i class="fas fa-credit-card"></i> Card
                        </button>
                        <button
                            data-payment="mobile"
                            class="payment-method-btn bg-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:opacity-90 transition">
                            <i class="fas fa-mobile-alt"></i> Mobile
                        </button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-2">
                    <button
                        id="holdOrderBtn"
                        disabled
                        class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition">
                        <i class="fas fa-pause mr-2"></i>Hold
                    </button>
                    <button
                        id="completeOrderBtn"
                        disabled
                        class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition">
                        <i class="fas fa-check mr-2"></i>Complete
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Variant Selection Modal --}}
    <div id="variantModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 id="modalProductName" class="text-xl font-bold text-gray-900"></h2>
                        <p class="text-gray-500 text-sm mt-1">Select size and color</p>
                    </div>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <div id="modalContent">
                    {{-- Product Image --}}
                    <div class="mb-6">
                        <img id="modalProductImage" src="" alt="" class="w-full h-64 object-cover rounded-lg">
                    </div>

                    {{-- Variants List --}}
                    <div class="space-y-3">
                        <h3 class="font-semibold text-gray-900 mb-3">Available Variants</h3>
                        <div id="variantsList" class="grid gap-2">
                            {{-- Variants will be rendered here --}}
                        </div>

                        <div id="noVariantsMessage" class="hidden text-center py-8">
                            <p class="text-gray-500">No variants available for this product</p>
                            <button id="addWithoutVariantBtn" class="mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                                Add to Cart - ৳<span id="noVariantPrice">0.00</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Data
        //var allProducts = @json($products);
        var cart = [];
        var selectedProduct = null;
        var paymentMethod = 'cash';
        var discount = 0;

        const defaultImage = "{{ asset('assets/images/default.png') }}";

        // Initialize
        function init() {
            attachProductCardHandlers();
        }

        // Attach click handlers to product cards
        function attachProductCardHandlers() {
            $('.product-card').on('click', function() {
                var productData = $(this).data('product');
                if (productData) {
                    selectProduct(productData);
                }
            });
        }

        // Search and Filter
        function filterProducts() {
            var searchQuery = $('#searchInput').val().toLowerCase();
            var categoryId = $('#categoryFilter').val();
            var visibleCount = 0;

            $('.product-card').each(function() {
                var $card = $(this);
                var productData = $card.data('product');
                
                if (!productData) return;

                var matchesSearch = !searchQuery ||
                    productData.name.toLowerCase().includes(searchQuery) ||
                    (productData.sku && productData.sku.toLowerCase().includes(searchQuery));

                var matchesCategory = !categoryId || productData.category_id == categoryId;

                if (matchesSearch && matchesCategory) {
                    $card.show();
                    visibleCount++;
                } else {
                    $card.hide();
                }
            });

            // Show/hide no products message
            if (visibleCount === 0) {
                $('#noProducts').removeClass('hidden').addClass('flex');
            } else {
                $('#noProducts').removeClass('flex').addClass('hidden');
            }
        }

        $('#searchInput').on('input', filterProducts);
        $('#categoryFilter').on('change', filterProducts);

        // Product Selection
        function selectProduct(product) {
            if (product.variants && product.variants.length > 0) {
                selectedProduct = product;
                showVariantModal(product);
            } else {
                addToCartWithoutVariant(product);
            }
        }

        // Show Variant Modal
        function showVariantModal(product) {
            $('#modalProductName').text(product.name);
            $('#modalProductImage').attr('src', product.thumbnail).attr('alt', product.name);

            var $variantsList = $('#variantsList');
            $variantsList.empty();

            if (product.variants && product.variants.length > 0) {
                $('#noVariantsMessage').addClass('hidden');

                product.variants.forEach(function(variant) {
                    var disabled = variant.stock <= 0 ? 'disabled' : '';
                    var borderClass = variant.stock > 0 ? 'border-gray-200' : 'border-red-200 bg-red-50';
                    var stockText = variant.stock > 0 ? `Stock: ${variant.stock}` : 'Out of Stock';
                    var stockClass = variant.stock > 0 ? 'text-green-600' : 'text-red-600';

                    var variantBtn = `
                    <button class="variant-btn flex items-center justify-between p-4 border-2 rounded-lg hover:border-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed ${borderClass}" 
                            data-variant-id="${variant.id}" ${disabled}>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded border-2 border-gray-300" style="background-color: ${variant.hex_code}"></div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">${variant.size_name} - ${variant.color_name}</p>
                                <p class="text-sm text-gray-500">SKU: ${variant.sku}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">৳${parseFloat(variant.price).toFixed(2)}</p>
                            <p class="text-xs ${stockClass}">${stockText}</p>
                        </div>
                    </button>
                `;

                    $variantsList.append(variantBtn);
                });

                // Attach click handlers
                $('.variant-btn').on('click', function() {
                    if (!$(this).is(':disabled')) {
                        var variantId = $(this).data('variant-id');
                        var variant = product.variants.find(v => v.id === variantId);
                        if (variant) {
                            addToCart(product, variant);
                        }
                    }
                });
            } else {
                $('#noVariantsMessage').removeClass('hidden');
                $('#noVariantPrice').text(parseFloat(product.price).toFixed(2));
            }

            $('#variantModal').removeClass('hidden');
        }

        // Close Modal
        $('#closeModalBtn, #variantModal').on('click', function(e) {
            if (e.target === this) {
                $('#variantModal').addClass('hidden');
                selectedProduct = null;
            }
        });

        // Add to Cart with Variant
        function addToCart(product, variant) {
            var existingItemIndex = cart.findIndex(item =>
                item.product_id === product.id && item.variant_id === variant.id
            );

            if (existingItemIndex !== -1) {
                if (cart[existingItemIndex].quantity < variant.stock) {
                    cart[existingItemIndex].quantity++;
                } else {
                    alert('Cannot add more than available stock');
                    return;
                }
            } else {
                cart.push({
                    product_id: product.id,
                    variant_id: variant.id,
                    name: product.name,
                    variant_name: (variant.size_name ? variant.size_name : '') + ' - ' + (variant.color_name ? variant.color_name : ''),
                    image: product.thumbnail,
                    price: parseFloat(variant.price),
                    quantity: 1,
                    stock: variant.stock
                });
            }

            $('#variantModal').addClass('hidden');
            selectedProduct = null;
            renderCart();
            updateTotals();
        }

        // Add to Cart without Variant
        $('#addWithoutVariantBtn').on('click', function() {
            if (selectedProduct) {
                addToCartWithoutVariant(selectedProduct);
            }
        });

        function addToCartWithoutVariant(product) {
            var existingItemIndex = cart.findIndex(item =>
                item.product_id === product.id && !item.variant_id
            );

            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity++;
            } else {
                cart.push({
                    product_id: product.id,
                    variant_id: null,
                    name: product.name,
                    variant_name: 'Standard',
                    image: product.image,
                    price: parseFloat(product.price),
                    quantity: 1,
                    stock: product.stock_in || 999
                });
            }

            $('#variantModal').addClass('hidden');
            selectedProduct = null;
            renderCart();
            updateTotals();
        }

        // Render Cart
        function renderCart() {
            var $cartItems = $('#cartItems');
            $cartItems.empty();

            $('#cartCount').text(cart.length);

            if (cart.length === 0) {
                $('#emptyCart').show();
                $('#holdOrderBtn, #completeOrderBtn').prop('disabled', true);
            } else {
                $('#emptyCart').hide();
                $('#holdOrderBtn, #completeOrderBtn').prop('disabled', false);

                cart.forEach(function(item, index) {
                    var itemTotal = (item.price * item.quantity).toFixed(2);
                    var itemPrice = item.price.toFixed(2);

                    var cartItem = `
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex gap-3">
                            <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded object-cover">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 truncate">${item.name}</h4>
                                <p class="text-xs text-gray-500">${item.variant_name}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button class="decrease-qty w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center text-gray-700" data-index="${index}">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" class="qty-input w-14 text-center py-1 border border-gray-300 rounded text-sm font-semibold" 
                                           value="${item.quantity}" min="1" max="${item.stock}" data-index="${index}">
                                    <button class="increase-qty w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center text-gray-700" data-index="${index}">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    <button class="remove-item ml-auto text-red-500 hover:text-red-700 p-1" data-index="${index}">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">৳${itemTotal}</p>
                                <p class="text-xs text-gray-500">৳${itemPrice} each</p>
                            </div>
                        </div>
                    </div>
                `;

                    $cartItems.append(cartItem);
                });

                // Attach cart item handlers
                $('.decrease-qty').on('click', function() {
                    var index = $(this).data('index');
                    decreaseQuantity(index);
                });

                $('.increase-qty').on('click', function() {
                    var index = $(this).data('index');
                    increaseQuantity(index);
                });

                $('.qty-input').on('change', function() {
                    var index = $(this).data('index');
                    var newQty = parseInt($(this).val());
                    updateQuantity(index, newQty);
                });

                $('.remove-item').on('click', function() {
                    var index = $(this).data('index');
                    removeItem(index);
                });
            }
        }

        // Cart Actions
        function increaseQuantity(index) {
            if (cart[index].quantity < cart[index].stock) {
                cart[index].quantity++;
                renderCart();
                updateTotals();
            } else {
                alert('Cannot add more than available stock');
            }
        }

        function decreaseQuantity(index) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
                renderCart();
                updateTotals();
            } else {
                removeItem(index);
            }
        }

        function updateQuantity(index, newQty) {
            if (newQty < 1) {
                cart[index].quantity = 1;
            } else if (newQty > cart[index].stock) {
                cart[index].quantity = cart[index].stock;
                alert('Cannot exceed available stock');
            } else {
                cart[index].quantity = newQty;
            }
            renderCart();
            updateTotals();
        }

        function removeItem(index) {
            if (confirm('Remove this item from cart?')) {
                cart.splice(index, 1);
                renderCart();
                updateTotals();
            }
        }

        // Clear Cart
        $('#clearCartBtn').on('click', function() {
            if (cart.length > 0) {
                if (confirm('Clear all items from cart?')) {
                    cart = [];
                    $('#customerName').val('');
                    discount = 0;
                    $('#discountCode').val('');
                    renderCart();
                    updateTotals();
                }
            }
        });

        // Update Totals
        function updateTotals() {
            var subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            var total = Math.max(0, subtotal - discount);

            $('#subtotalAmount').text(subtotal.toFixed(2));
            $('#totalAmount').text(total.toFixed(2));

            if (discount > 0) {
                $('#discountAmount').text(discount.toFixed(2));
                $('#discountDisplay').text(discount.toFixed(2));
                $('#discountApplied').removeClass('hidden');
                $('#discountRow').removeClass('hidden');
            } else {
                $('#discountApplied').addClass('hidden');
                $('#discountRow').addClass('hidden');
            }
        }

        // Apply Discount
        $('#applyDiscountBtn').on('click', function() {
            var discountCode = $('#discountCode').val();
            if (!discountCode) return;

            var subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            if (discountCode.includes('%')) {
                var percent = parseFloat(discountCode.replace('%', ''));
                if (!isNaN(percent) && percent > 0 && percent <= 100) {
                    discount = subtotal * (percent / 100);
                    alert(percent + '% discount applied');
                    updateTotals();
                } else {
                    alert('Invalid percentage');
                }
            } else {
                var amount = parseFloat(discountCode);
                if (!isNaN(amount) && amount > 0) {
                    discount = Math.min(amount, subtotal);
                    alert('৳' + amount + ' discount applied');
                    updateTotals();
                } else {
                    alert('Invalid discount code');
                }
            }
        });

        // Payment Method
        $('.payment-method-btn').on('click', function() {
            $('.payment-method-btn').removeClass('bg-blue-600 text-white').addClass('bg-gray-200 text-gray-700');
            $(this).removeClass('bg-gray-200 text-gray-700').addClass('bg-blue-600 text-white');
            paymentMethod = $(this).data('payment');
        });

        // Hold Order
        $('#holdOrderBtn').on('click', function() {
            if (cart.length === 0) return;

            var subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            var total = Math.max(0, subtotal - discount);

            var holdOrders = JSON.parse(localStorage.getItem('holdOrders') || '[]');
            holdOrders.push({
                cart: cart,
                customer: $('#customerName').val(),
                subtotal: subtotal,
                discount: discount,
                total: total,
                timestamp: new Date().toISOString()
            });
            localStorage.setItem('holdOrders', JSON.stringify(holdOrders));

            alert('Order held successfully');
            cart = [];
            $('#customerName').val('');
            discount = 0;
            $('#discountCode').val('');
            renderCart();
            updateTotals();
        });

        // Complete Order
        $('#completeOrderBtn').on('click', function() {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }

            var subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            var total = Math.max(0, subtotal - discount);

            if (confirm('Complete order for ৳' + total.toFixed(2) + '?')) {
                var order = {
                    customer_name: $('#customerName').val() || 'Walk-in Customer',
                    payment_method: paymentMethod,
                    items: cart,
                    subtotal: subtotal,
                    discount: discount,
                    total: total,
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("admin.pos.store") }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify(order),
                    success: function(data) {
                        if (data.success) {
                            alert('Order completed successfully!');
                            cart = [];
                            $('#customerName').val('');
                            discount = 0;
                            $('#discountCode').val('');
                            renderCart();
                            updateTotals();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to complete order'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Failed to complete order. Please try again.');
                    }
                });
            }
        });

        // Initialize the POS system
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