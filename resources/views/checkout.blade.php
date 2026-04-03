@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ url('/') }}" class="text-gray-500 hover:text-brand-blue transition">Home</a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <a href="#" onclick="openCartDrawer(); return false;" class="text-gray-500 hover:text-brand-blue transition">Cart</a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <span class="text-gray-900 font-medium">Checkout</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-4">
    {{-- Checkout Header --}}
    <!-- <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-brand-black mb-2">Checkout</h1>
        <p class="text-gray-500 text-sm">Complete your order by filling in the details below</p>
    </div> -->

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
            <div class="flex-1">
                <h4 class="font-semibold text-red-800 mb-2">Please fix the following errors:</h4>
                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-3 gap-6 lg:gap-8">
        @csrf
        {{-- Left Column - Customer Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Customer Information --}}
            {{--<div class="bg-white rounded-2xl p-5 md:p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-lg font-bold text-brand-black">Customer Information</h2>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Enter your full name" value="{{ old('name', $user?->name) }}" class="w-full h-12 px-4 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition @error('name') border-red-500 @else border-gray-200 @enderror">
                        @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="h-12 px-3 bg-gray-100 border border-r-0 rounded-l-xl flex items-center text-sm text-gray-600 @error('phone') border-red-500 @else border-gray-200 @enderror">+88</span>
                            <input type="tel" name="phone" required placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" maxlength="11" value="{{ old('phone', $user?->phone) }}" class="flex-1 h-12 px-4 border rounded-r-xl text-sm focus:outline-none focus:border-brand-blue transition @error('phone') border-red-500 @else border-gray-200 @enderror">
                        </div>
                        @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @else
                        <p class="text-xs text-gray-400 mt-1">We'll contact you on this number</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-gray-400">(Optional)</span></label>
                        <input type="email" name="email" placeholder="your@email.com" value="{{ old('email', $user?->email) }}" class="w-full h-12 px-4 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition @error('email') border-red-500 @else border-gray-200 @enderror">
                        @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>--}}

            {{-- Delivery Address --}}
            <div class="bg-white rounded-2xl p-5 md:p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-lg font-bold text-brand-black">Delivery Informations</h2>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Enter your full name" value="{{ old('name', $user?->name) }}" class="w-full h-12 px-4 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition @error('name') border-red-500 @else border-gray-200 @enderror">
                        @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="h-12 px-3 bg-gray-100 border border-r-0 rounded-l-xl flex items-center text-sm text-gray-600 @error('phone') border-red-500 @else border-gray-200 @enderror">+88</span>
                            <input type="tel" name="phone" required placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" maxlength="11" value="{{ old('phone', $user?->phone) }}" class="flex-1 h-12 px-4 border rounded-r-xl text-sm focus:outline-none focus:border-brand-blue transition @error('phone') border-red-500 @else border-gray-200 @enderror">
                        </div>
                        @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @else
                        <p class="text-xs text-gray-400 mt-1">We'll contact you on this number</p>
                        @enderror
                    </div>
                </div>

                {{-- Delivery Zone Selection --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Delivery Zone <span class="text-red-500">*</span></label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($shippingZones as $index => $zone)
                        <label class="delivery-zone-option relative cursor-pointer">
                            <input type="radio" name="delivery_zone" value="{{ $zone->code }}" data-cost="{{ $zone->shipping_cost }}" class="sr-only peer" {{ $index === 0 ? 'checked' : '' }}>
                            <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-900">{{ $zone->name }}</span>
                                    <span class="text-brand-blue font-bold ms-2">({{ money($zone->shipping_cost) }})</span>
                                </div>
                                <p class="text-xs text-gray-500">Delivery within {{ $zone->estimated_days }}</p>
                            </div>
                            <div class="absolute top-3 right-3 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District <span class="text-red-500">*</span></label>
                        <select name="district" required class="w-full h-12 px-4 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition bg-white @error('district') border-red-500 @else border-gray-200 @enderror">
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district') == $district->id ? 'selected' : '' }}>
                                {{ $district->display_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('district')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City/Area <span class="text-red-500">*</span></label>
                        <input type="text" name="city" required placeholder="e.g., Uttara, Mirpur, Dhanmondi" value="{{ old('city') }}" class="w-full h-12 px-4 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition @error('city') border-red-500 @else border-gray-200 @enderror">
                        @error('city')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Address <span class="text-red-500">*</span></label>
                        <textarea name="address" required rows="3" placeholder="House/Flat No, Road, Block, Area (Be specific for faster delivery)" class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition resize-none @error('address') border-red-500 @else border-gray-200 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
           

            {{-- Order Notes --}}
            <div class="bg-white rounded-2xl p-5 md:p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <h2 class="text-lg font-bold text-brand-black">Order Notes <span class="text-gray-400 font-normal text-sm">(Optional)</span></h2>
                </div>
                <textarea name="notes" rows="3" placeholder="Any special instructions for your order? e.g., Gift wrapping, specific delivery time, etc." class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:border-brand-blue transition resize-none @error('notes') border-red-500 @else border-gray-200 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Right Column - Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-5 md:p-6 border border-gray-100 shadow-sm sticky top-24">
                <h2 class="text-lg font-bold text-brand-black mb-5">Order Summary</h2>

                {{-- Cart Items --}}
                <div class="space-y-4 max-h-64 overflow-y-auto mb-5 pr-2">
                    @foreach($cart->items as $item)
                    <div class="flex gap-3">
                        <div class="w-16 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $item->product->name }}</h4>
                            @if($item->variant)
                            <p class="text-xs text-gray-500 mt-1">
                                @if($item->variant->size)
                                Size: {{ $item->variant->size->name }}
                                @endif
                                @if($item->variant->size && $item->variant->color) | @endif
                                @if($item->variant->color)
                                Color: {{ $item->variant->color->name }}
                                @endif
                            </p>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500">Qty: {{ $item->quantity }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ money($item->total_price) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="#" onclick="openCartDrawer(); return false;" class="text-brand-blue text-sm font-medium hover:underline mb-5 inline-block">
                    <i class="fas fa-edit mr-1"></i> Edit Cart
                </a>

                <div class="mb-5">
                    <div class="flex gap-2">
                        <input type="text" name="coupon" id="couponCode" placeholder="Coupon code" class="flex-1 h-11 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-blue transition">
                        <button type="button" id="applyCouponBtn" class="h-11 px-4 bg-gray-100 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-200 transition">Apply</button>
                    </div>
                    <div id="couponMessage" class="mt-2 text-sm hidden"></div>
                </div>

                {{-- Price Breakdown --}}
                <div class="space-y-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal ({{ $cart->items_count }} {{ Str::plural('item', $cart->items_count) }})</span>
                        <span class="font-medium text-gray-900" id="subtotalAmount">{{ money($cart->subtotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Shipping</span>
                        <span class="font-medium text-gray-900" id="shippingCost">{{ money($shippingZones->first()->shipping_cost ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-green-600" id="discountRow" style="display: none;">
                        <span>Discount</span>
                        <span class="font-medium" id="discountAmount">-৳0</span>
                    </div>
                    <div class="h-px bg-gray-200"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-brand-blue" id="totalAmount">{{ money($cart->subtotal + ($shippingZones->first()->shipping_cost ?? 0)) }}</span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method <span class="text-red-500">*</span></label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="payment-method-option relative cursor-pointer">
                            <input type="radio" name="payment_method" value="cod"  class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-900">COD</span>
                                </div>
                                <p class="text-xs text-gray-500">Cash on delivery.</p>
                            </div>
                            <div class="absolute top-3 right-3 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                            </div>
                        </label>
                        <label class="payment-method-option relative cursor-pointer">
                            <input type="radio" name="payment_method" value="online"  class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-900">Pay Now</span>
                                </div>
                                <p class="text-xs text-gray-500">Payment online.</p>
                            </div>
                            <div class="absolute top-3 right-3 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Place Order Button --}}
                <button type="submit" class="w-full mt-6 bg-brand-blue text-white py-4 rounded-xl font-semibold text-base hover:bg-blue-600 transition tap-effect shadow-lg shadow-brand-blue/25 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Place Order
                </button>

                {{-- Terms --}}
                <p class="text-xs text-gray-400 text-center mt-4">
                    By placing this order, you agree to our
                    <a href="#" class="text-brand-blue hover:underline">Terms & Conditions</a> and
                    <a href="#" class="text-brand-blue hover:underline">Privacy Policy</a>
                </p>

                {{-- Trust Badges --}}
                <div class="flex items-center justify-center gap-4 mt-5 pt-5 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span class="text-xs">Secure</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-undo text-brand-blue"></i>
                        <span class="text-xs">7 Days Return</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-truck text-orange-500"></i>
                        <span class="text-xs">Fast Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Cart data from server
    const cartSubtotal = "{{ $cart->subtotal }}";
    let currentDiscount = 0;
    let currentShippingCost = "{{ $shippingZones->first()->shipping_cost ?? 0 }}";
    let appliedCouponCode = '';

    // Update total calculations
    function updateTotals() {
        const total = parseFloat(cartSubtotal) + parseFloat(currentShippingCost) - parseFloat(currentDiscount);
        document.getElementById('totalAmount').textContent = '৳' + total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Payment method toggle
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('bkashInstructions').classList.add('hidden');
            document.getElementById('nagadInstructions').classList.add('hidden');

            if (this.value === 'bkash') {
                document.getElementById('bkashInstructions').classList.remove('hidden');
            } else if (this.value === 'nagad') {
                document.getElementById('nagadInstructions').classList.remove('hidden');
            }
        });
    });

    // Delivery zone shipping cost update
    document.querySelectorAll('input[name="delivery_zone"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentShippingCost = parseFloat(this.dataset.cost);
            document.getElementById('shippingCost').textContent = '৳' + currentShippingCost.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            updateTotals();
        });
    });

    // Coupon validation
    document.getElementById('applyCouponBtn').addEventListener('click', async function() {
        const couponInput = document.getElementById('couponCode');
        const couponCode = couponInput.value.trim();
        const messageDiv = document.getElementById('couponMessage');
        const btn = this;

        if (!couponCode) {
            showCouponMessage('Please enter a coupon code', 'error');
            return;
        }

        // Disable button during validation
        btn.disabled = true;
        btn.textContent = 'Checking...';

        try {
            const response = await fetch("{{ route('checkout.validate-coupon') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        coupon: couponCode,
                        subtotal: cartSubtotal
                    })
                });

            const data = await response.json();

            if (response.ok && data.success) {
                currentDiscount = data.discount;
                appliedCouponCode = couponCode;

                // Show discount in UI
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmount').textContent = '-' + data.discount_formatted;

                // Update totals
                updateTotals();

                // Show success message
                showCouponMessage(data.message, 'success');

                // Change button to "Remove"
                btn.textContent = 'Remove';
                btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                btn.classList.add('bg-red-100', 'text-red-700', 'hover:bg-red-200');
                btn.onclick = removeCoupon;

                // Disable input
                couponInput.disabled = true;
            } else {
                showCouponMessage(data.message || 'Invalid coupon code', 'error');
                btn.textContent = 'Apply';
            }
        } catch (error) {
            showCouponMessage('Error validating coupon. Please try again.', 'error');
            btn.textContent = 'Apply';
        } finally {
            btn.disabled = false;
        }
    });

    // Remove coupon
    function removeCoupon() {
        const btn = document.getElementById('applyCouponBtn');
        const couponInput = document.getElementById('couponCode');

        currentDiscount = 0;
        appliedCouponCode = '';

        // Hide discount row
        document.getElementById('discountRow').style.display = 'none';

        // Update totals
        updateTotals();

        // Reset button
        btn.textContent = 'Apply';
        btn.classList.remove('bg-red-100', 'text-red-700', 'hover:bg-red-200');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        btn.onclick = null;

        // Enable and clear input
        couponInput.disabled = false;
        couponInput.value = '';

        // Hide message
        document.getElementById('couponMessage').classList.add('hidden');
    }

    // Show coupon message
    function showCouponMessage(message, type) {
        const messageDiv = document.getElementById('couponMessage');
        messageDiv.textContent = message;
        messageDiv.classList.remove('hidden', 'text-green-600', 'text-red-600');
        messageDiv.classList.add(type === 'success' ? 'text-green-600' : 'text-red-600');
    }

    // Copy to clipboard
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            icon.classList.remove('far', 'fa-copy');
            icon.classList.add('fas', 'fa-check');

            setTimeout(() => {
                icon.classList.remove('fas', 'fa-check');
                icon.classList.add('far', 'fa-copy');
            }, 2000);
        });
    }

    // Form validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (paymentMethod === 'bkash') {
            const trxId = document.querySelector('input[name="bkash_trx_id"]').value;
            if (!trxId) {
                alert('Please enter your bKash Transaction ID');
                return;
            }
        } else if (paymentMethod === 'nagad') {
            const trxId = document.querySelector('input[name="nagad_trx_id"]').value;
            if (!trxId) {
                alert('Please enter your Nagad Transaction ID');
                return;
            }
        }

        // Submit the form
        this.submit();
    });

    // Update radio button checkmarks visibility
    document.querySelectorAll('.delivery-zone-option input, .payment-option input').forEach(input => {
        const updateCheckmark = () => {
            document.querySelectorAll('.delivery-zone-option, .payment-option').forEach(label => {
                const check = label.querySelector('.fa-check');
                const radio = label.querySelector('input[type="radio"]');
                if (check && radio) {
                    check.parentElement.classList.toggle('bg-brand-blue', radio.checked);
                    check.parentElement.classList.toggle('border-brand-blue', radio.checked);
                    check.classList.toggle('hidden', !radio.checked);
                }
            });
        };
        input.addEventListener('change', updateCheckmark);
        updateCheckmark();
    });
</script>
@endpush