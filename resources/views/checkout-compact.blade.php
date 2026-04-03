@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-50 min-h-screen pb-10">
    {{-- Compact Breadcrumb --}}
    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-medium">
                <a href="{{ url('/') }}" class="text-gray-500 hover:text-brand-blue">Home</a>
                <span class="text-gray-300">/</span>
                <a href="#" onclick="openCartDrawer(); return false;" class="text-gray-500 hover:text-brand-blue">Cart</a>
                <span class="text-gray-300">/</span>
                <span class="text-brand-blue">Checkout</span>
            </nav>
            <div class="text-xs text-gray-400 hidden sm:block">
                Secure SSL Encrypted Transaction
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6">
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-sm text-red-700 font-medium">Please fix the errors below.</span>
        </div>
        @endif

        <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-12 gap-5 items-start">
            @csrf

            {{-- Left Column: All Inputs --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Section 1: Customer & Address Combined (Dense Grid) --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex items-center gap-2">
                        <div class="w-5 h-5 bg-brand-blue text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Shipping Information</h2>
                    </div>

                    <div class="p-4 grid gap-4">
                        {{-- Row 1: Name, Phone, Email --}}
                        <div class="grid sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required value="{{ old('name', $user?->name) }}" class="w-full h-9 px-3 border rounded-md text-sm focus:ring-1 focus:ring-brand-blue focus:border-brand-blue border-gray-300" placeholder="Full Name">
                                @error('name') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Phone <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <span class="h-9 px-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md flex items-center text-xs text-gray-500">+88</span>
                                    <input type="tel" name="phone" required value="{{ old('phone', $user?->phone) }}" class="flex-1 h-9 px-3 border rounded-r-md text-sm focus:ring-1 focus:ring-brand-blue focus:border-brand-blue border-gray-300" placeholder="01XXX...">
                                </div>
                                @error('phone') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Email <span class="text-gray-400 font-normal">(Opt)</span></label>
                                <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="w-full h-9 px-3 border rounded-md text-sm focus:ring-1 focus:ring-brand-blue focus:border-brand-blue border-gray-300" placeholder="Email Address">
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Row 2: Delivery Zone (Horizontal Pills) --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Delivery Zone <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($shippingZones as $index => $zone)
                                <label class="relative cursor-pointer group flex-1 sm:flex-none">
                                    <input type="radio" name="delivery_zone" value="{{ $zone->code }}" data-cost="{{ $zone->shipping_cost }}" class="sr-only peer" {{ $index === 0 ? 'checked' : '' }}>
                                    <div class="px-3 py-2 border border-gray-200 rounded-md peer-checked:border-brand-blue peer-checked:bg-blue-50 peer-checked:text-brand-blue hover:bg-gray-50 transition text-center min-w-[120px]">
                                        <div class="text-xs font-bold">{{ $zone->name }}</div>
                                        <div class="text-[10px] text-gray-500 peer-checked:text-blue-600">{{ money($zone->shipping_cost) }} - {{ $zone->estimated_days }}</div>
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-brand-blue rounded-full items-center justify-center hidden peer-checked:flex">
                                        <i class="fas fa-check text-white text-[8px]"></i>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Row 3: District, City, Address --}}
                        <div class="grid sm:grid-cols-4 gap-3">
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">District <span class="text-red-500">*</span></label>
                                <select name="district" required class="w-full h-9 px-2 border rounded-md text-sm focus:ring-1 focus:ring-brand-blue border-gray-300 bg-white">
                                    <option value="">Select</option>
                                    @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district') == $district->id ? 'selected' : '' }}>{{ $district->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Area/City <span class="text-red-500">*</span></label>
                                <input type="text" name="city" required value="{{ old('city') }}" class="w-full h-9 px-3 border rounded-md text-sm focus:ring-1 focus:ring-brand-blue border-gray-300" placeholder="e.g. Uttara">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Full Address <span class="text-red-500">*</span></label>
                                <input type="text" name="address" required value="{{ old('address') }}" class="w-full h-9 px-3 border rounded-md text-sm focus:ring-1 focus:ring-brand-blue border-gray-300" placeholder="House, Road, Block...">
                                @error('address') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Payment & Notes --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex items-center gap-2">
                        <div class="w-5 h-5 bg-brand-blue text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Payment Method</h2>
                    </div>

                    <div class="p-4">
                        {{-- Horizontal Payment Tabs --}}
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            {{-- COD --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" class="sr-only peer" checked onchange="togglePaymentDetails('cod')">
                                <div class="h-full flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                                    <i class="fas fa-money-bill-wave text-green-600 text-lg mb-1"></i>
                                    <span class="text-xs font-bold text-gray-700">Cash on Delivery</span>
                                </div>
                            </label>

                            {{-- bKash --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="bkash" class="sr-only peer" onchange="togglePaymentDetails('bkash')">
                                <div class="h-full flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 peer-checked:border-pink-500 peer-checked:bg-pink-50 transition">
                                    <img src="{{ asset('assets/images/bkash.png') }}" alt="bKash" class="h-5 mb-1 object-contain">
                                    <span class="text-xs font-bold text-gray-700">bKash</span>
                                </div>
                            </label>

                            {{-- Nagad --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="nagad" class="sr-only peer" onchange="togglePaymentDetails('nagad')">
                                <div class="h-full flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition">
                                    <img src="{{ asset('assets/images/nagad.png') }}" alt="Nagad" class="h-5 mb-1 object-contain">
                                    <span class="text-xs font-bold text-gray-700">Nagad</span>
                                </div>
                            </label>
                        </div>

                        {{-- Compact bKash Instructions --}}
                        <div id="bkashInstructions" class="hidden bg-pink-50 rounded-lg p-4 border border-pink-100 text-sm animate-fade-in-down">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center justify-between text-xs text-pink-800 bg-white p-2 rounded border border-pink-200">
                                        <span class="font-bold">Merchant Number:</span>
                                        <div class="flex items-center gap-2">
                                            <code class="font-mono font-bold text-base">{{ $bkashNumber }}</code>
                                            <button type="button" onclick="copyToClipboard('{{ str_replace('-', '', $bkashNumber) }}')" class="text-pink-500 hover:text-pink-700"><i class="far fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <ol class="list-decimal list-inside text-xs text-pink-800 space-y-1 pl-1">
                                        <li>Dial *247# or use App -> <strong>Send Money</strong></li>
                                        <li>Reference: Your Phone Number</li>
                                    </ol>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-pink-800 mb-1">Transaction ID (TrxID) <span class="text-red-500">*</span></label>
                                    <input type="text" name="bkash_trx_id" placeholder="e.g. 8N72KS92JD" class="w-full h-9 px-3 border border-pink-300 rounded-md text-sm uppercase focus:ring-pink-500 focus:border-pink-500">
                                </div>
                            </div>
                        </div>

                        {{-- Compact Nagad Instructions --}}
                        <div id="nagadInstructions" class="hidden bg-orange-50 rounded-lg p-4 border border-orange-100 text-sm animate-fade-in-down">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center justify-between text-xs text-orange-800 bg-white p-2 rounded border border-orange-200">
                                        <span class="font-bold">Nagad Number:</span>
                                        <div class="flex items-center gap-2">
                                            <code class="font-mono font-bold text-base">{{ $nagadNumber }}</code>
                                            <button type="button" onclick="copyToClipboard('{{ str_replace('-', '', $nagadNumber) }}')" class="text-orange-500 hover:text-orange-700"><i class="far fa-copy"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-orange-800 mb-1">Transaction ID (TrxID) <span class="text-red-500">*</span></label>
                                    <input type="text" name="nagad_trx_id" placeholder="e.g. N8K72J92" class="w-full h-9 px-3 border border-orange-300 rounded-md text-sm uppercase focus:ring-orange-500 focus:border-orange-500">
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Order Notes (Optional)</label>
                            <input type="text" name="notes" placeholder="Special instructions..." class="w-full h-9 px-3 border rounded-md text-xs border-gray-200 focus:border-brand-blue">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-20">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Order Summary</h2>
                    </div>

                    {{-- Scrollable Cart List --}}
                    <div class="max-h-[240px] overflow-y-auto p-4 space-y-3 custom-scrollbar">
                        @foreach($cart->items as $item)
                        <div class="flex gap-3 items-center group">
                            <div class="w-10 h-12 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                                <img src="{{ $item->product->thumbnail }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-medium text-gray-800 truncate">{{ $item->product->name }}</h4>
                                <div class="text-[10px] text-gray-500">
                                    Qty: {{ $item->quantity }}
                                    @if($item->variant)
                                    | {{ $item->variant->size->name ?? '' }} {{ $item->variant->color->name ?? '' }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-gray-900">{{ money($item->total_price) }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Totals Section --}}
                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                        {{-- Coupon --}}
                        <div class="flex gap-2 mb-4">
                            <input type="text" name="coupon" id="couponCode" placeholder="Coupon Code" class="flex-1 h-8 px-3 text-xs border border-gray-300 rounded focus:border-brand-blue">
                            <button type="button" id="applyCouponBtn" class="h-8 px-3 bg-gray-800 text-white text-xs rounded hover:bg-gray-700 transition">Apply</button>
                        </div>
                        <div id="couponMessage" class="text-xs mb-2 hidden"></div>

                        <div class="space-y-1.5 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-medium text-gray-900" id="subtotalAmount">{{ money($cart->subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span class="font-medium text-gray-900" id="shippingCost">{{ money($shippingZones->first()->shipping_cost ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between text-green-600 hidden" id="discountRow">
                                <span>Discount</span>
                                <span class="font-bold" id="discountAmount">-৳0</span>
                            </div>
                        </div>

                        <div class="h-px bg-gray-200 my-3"></div>

                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-brand-blue" id="totalAmount">{{ money($cart->subtotal + ($shippingZones->first()->shipping_cost ?? 0)) }}</span>
                        </div>

                        <button type="submit" class="w-full bg-brand-blue text-white py-3 rounded-lg font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Confirm Order
                        </button>

                        <div class="mt-3 flex justify-center gap-3 opacity-50 grayscale hover:grayscale-0 transition duration-300">
                            <i class="fab fa-cc-visa text-xl"></i>
                            <i class="fab fa-cc-mastercard text-xl"></i>
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple toggle script for payment methods
    function togglePaymentDetails(method) {
        document.getElementById('bkashInstructions').classList.add('hidden');
        document.getElementById('nagadInstructions').classList.add('hidden');

        if (method === 'bkash') {
            document.getElementById('bkashInstructions').classList.remove('hidden');
        } else if (method === 'nagad') {
            document.getElementById('nagadInstructions').classList.remove('hidden');
        }
    }

    // Make sure to add this styling for custom scrollbar if needed
    // .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    // .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
</script>
@endsection