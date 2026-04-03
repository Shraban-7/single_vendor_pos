@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-xl hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="downloadInvoice()" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-download"></i>
            <span>Download Invoice</span>
        </button>
        <!-- <button onclick="window.print()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Print Invoice</span>
        </button> -->
        @if($order->status->value !== 'cancelled')
        <button onclick="openDeleteModal()" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition flex items-center gap-2">
            <i class="fas fa-trash"></i>
            <span>Delete</span>
        </button>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left Column - Order Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Order Status --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                Order Status
            </h2>

            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $order->status->value === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status->value === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ $order->status->value === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status->value === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status->value === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
                        <input type="text" name="comment" placeholder="Add a note..." class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Update Status
                </button>
            </form>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-box text-blue-600"></i>
                Order Items ({{ $order->items->count() }})
            </h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-20 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-white">
                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 mb-1">{{ $item->product_name }}</h4>
                        @if($item->size_name || $item->color_name)
                        <p class="text-sm text-gray-500 mb-2">
                            @if($item->size_name)Size: {{ $item->size_name }}@endif
                            @if($item->size_name && $item->color_name) | @endif
                            @if($item->color_name)Color: {{ $item->color_name }}@endif
                        </p>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Qty: {{ $item->quantity }} × {{ money($item->unit_price) }}</span>
                            <span class="text-base font-bold text-gray-900">{{ money($item->total) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium text-gray-900">{{ money($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Shipping Cost</span>
                    <span class="font-medium text-gray-900">{{ money($order->shipping_cost) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span>
                    <span class="font-medium">-{{ money($order->discount_amount) }}</span>
                </div>
                @endif
                <div class="h-px bg-gray-200"></div>
                <div class="flex justify-between text-lg">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="text-2xl font-bold text-blue-600">{{ money($order->total) }}</span>
                </div>
            </div>
        </div>

        {{-- Status History --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i>
                Status History
            </h2>

            <div class="space-y-4">
                @forelse($order->statusHistories as $history)
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-circle text-blue-600 text-xs"></i>
                        </div>
                        @if(!$loop->last)
                        <div class="flex-1 w-0.5 bg-gray-200 my-1"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-start justify-between gap-4 mb-1">
                            <span class="font-semibold text-gray-900">{{ $history->status->label() }}</span>
                            <span class="text-sm text-gray-500">{{ $history->created_at->diffForHumans() }}</span>
                        </div>
                        @if($history->comment)
                        <p class="text-sm text-gray-600 mb-1">{{ $history->comment }}</p>
                        @endif
                        @if($history->updater)
                        <p class="text-xs text-gray-500">By: {{ $history->updater->name }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ $history->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No status history available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column - Customer & Shipping Info --}}
    <div class="space-y-6">
        {{-- Customer Information --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i>
                Customer Information
            </h2>

            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Name</p>
                    <p class="font-semibold text-gray-900">{{ $order->shipping_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Phone</p>
                    <a href="tel:{{ $order->shipping_phone }}" class="font-semibold text-blue-600 hover:text-blue-800">
                        {{ $order->shipping_phone }}
                    </a>
                </div>
                @if($order->shipping_email)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <a href="mailto:{{ $order->shipping_email }}" class="font-semibold text-blue-600 hover:text-blue-800">
                        {{ $order->shipping_email }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-blue-600"></i>
                Shipping Address
            </h2>

            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-900">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_district }}</p>
                </div>
                <div class="pt-3 border-t border-gray-200">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                        <i class="fas fa-truck"></i>
                        {{ $order->delivery_zone->label() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-credit-card text-blue-600"></i>
                Payment Information
            </h2>

            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 rounded-full text-sm font-medium">
                        {{ $order->payment_method->label() }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Payment Status</p>
                    @if($order->payment_status->value === 'paid')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle"></i>
                        Paid
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                        <i class="fas fa-clock"></i>
                        {{ $order->payment_status->label() }}
                    </span>
                    @endif
                </div>
                @if($order->transaction_id)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                    <code class="block bg-gray-50 px-3 py-2 rounded-lg text-sm font-mono">{{ $order->transaction_id }}</code>
                </div>
                @endif
                @if($order->paid_at)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Paid At</p>
                    <p class="text-sm text-gray-900">{{ $order->paid_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Tracking Information --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-shipping-fast text-blue-600"></i>
                Tracking Information
            </h2>

            @if($order->tracking_number)
            <div class="space-y-3 mb-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Courier</p>
                    <p class="font-semibold text-gray-900">{{ $order->courier }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Tracking Number</p>
                    <code class="block bg-gray-50 px-3 py-2 rounded-lg text-sm font-mono">{{ $order->tracking_number }}</code>
                </div>
            </div>
            @endif

            <form action="{{ route('admin.orders.update-tracking', $order->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Courier</label>
                    <input type="text" name="courier" value="{{ old('courier', $order->courier) }}" placeholder="e.g., Sundarban, Pathao" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Enter tracking number" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    <i class="fas fa-save mr-2"></i>Save Tracking Info
                </button>
            </form>
        </div>

        {{-- Admin Notes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-sticky-note text-blue-600"></i>
                Admin Notes
            </h2>

            <form action="{{ route('admin.orders.update-notes', $order->id) }}" method="POST">
                @csrf
                <textarea name="admin_notes" rows="4" placeholder="Add internal notes about this order..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                <button type="submit" class="mt-3 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    <i class="fas fa-save mr-2"></i>Save Notes
                </button>
            </form>
        </div>

        @if($order->notes)
        {{-- Customer Notes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-comment text-blue-600"></i>
                Customer Notes
            </h2>
            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Invoice Template (Hidden) - A4 Size --}}
<div id="invoiceTemplate" class="hidden">
    <div class="invoice-container" style="width: 210mm; padding: 12mm 15mm; background: white; font-family: 'Arial', 'Helvetica', sans-serif; color: #000;">
        {{-- Invoice Header --}}
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 3px solid #000;">
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; color: #000; margin: 0 0 3px 0; letter-spacing: -0.5px;">SPINNER FASHION</h1>
                <div style="width: 50px; height: 2px; background: #000; margin-bottom: 8px;"></div>
                <p style="font-size: 9px; color: #444; margin: 0; line-height: 1.6;">
                    123 Fashion Street, Dhaka 1215, Bangladesh<br>
                    Phone: +880 1711-123456 | Email: info@spinnerfashion.com<br>
                    Web: www.spinnerfashion.com
                </p>
            </div>
            <div style="text-align: right;">
                <h2 style="font-size: 30px; font-weight: 700; color: #000; margin: 0 0 5px 0; letter-spacing: 2px;">INVOICE</h2>
                <table style="margin-left: auto; border-collapse: collapse; margin-top: 10px;">
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Invoice No:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Invoice Date:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ $order->created_at->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Order Status:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ strtoupper($order->status->label()) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Bill To & Payment Info --}}
        <div style="display: flex; gap: 30px; margin-bottom: 20px;">
            <div style="flex: 1; border-left: 3px solid #000; padding-left: 10px;">
                <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">BILL TO</h3>
                <p style="font-size: 10px; color: #000; margin: 0; line-height: 1.7;">
                    <strong style="font-size: 12px; display: block; margin-bottom: 5px; color: #000;">{{ $order->shipping_name }}</strong>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_district }}@if($order->shipping_postal_code) - {{ $order->shipping_postal_code }}@endif<br>
                    <strong>Phone:</strong> {{ $order->shipping_phone }}@if($order->shipping_email)<br><strong>Email:</strong> {{ $order->shipping_email }}@endif
                </p>
            </div>
            <div style="flex: 1; border-left: 3px solid #000; padding-left: 10px;">
                <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">PAYMENT DETAILS</h3>
                <table style="width: 100%; font-size: 10px; line-height: 1.7;">
                    <tr>
                        <td style="color: #666; padding: 1px 0; width: 45%;">Payment Method:</td>
                        <td style="color: #000; padding: 1px 0; font-weight: 600;">{{ $order->payment_method->label() }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 1px 0;">Payment Status:</td>
                        <td style="color: #000; padding: 1px 0; font-weight: 600;">{{ strtoupper($order->payment_status->label()) }}</td>
                    </tr>
                    @if($order->transaction_id)
                    <tr>
                        <td style="color: #666; padding: 1px 0; vertical-align: top;">Transaction ID:</td>
                        <td style="color: #000; padding: 1px 0; font-family: 'Courier New', monospace; font-size: 8px; word-break: break-all; font-weight: 600;">{{ $order->transaction_id }}</td>
                    </tr>
                    @endif
                    @if($order->paid_at)
                    <tr>
                        <td style="color: #666; padding: 1px 0;">Paid Date:</td>
                        <td style="color: #000; padding: 1px 0; font-weight: 600;">{{ $order->paid_at->format('d M, Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Order Items Table --}}
        {{-- Order Items Table --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #000;">
                    <th style="padding: 10px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 5%;">SL</th>
                    <th style="padding: 10px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333;">Product Description</th>
                    <th style="padding: 10px 8px; text-align: center; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 8%;">Qty</th>
                    <th style="padding: 10px 8px; text-align: right; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 15%;">Unit Price</th>
                    <th style="padding: 10px 8px; text-align: right; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 8px; font-size: 9px; color: #666; border-right: 1px solid #e5e7eb; text-align: center;">{{ $index + 1 }}</td>
                    <td style="padding: 10px 8px; border-right: 1px solid #e5e7eb;">
                        <div style="font-size: 10px; font-weight: 600; color: #000; margin-bottom: 3px;">{{ $item->product_name }}</div>
                        @if($item->size_name || $item->color_name)
                        <div style="font-size: 8px; color: #666;">
                            @if($item->size_name)<span style="background: #f3f4f6; padding: 2px 5px; border-radius: 2px; margin-right: 3px;">Size: {{ $item->size_name }}</span>@endif
                            @if($item->color_name)<span style="background: #f3f4f6; padding: 2px 5px; border-radius: 2px;">Color: {{ $item->color_name }}</span>@endif
                        </div>
                        @endif
                    </td>
                    <td style="padding: 10px 8px; text-align: center; font-size: 10px; color: #000; font-weight: 600; border-right: 1px solid #e5e7eb;">{{ $item->quantity }}</td>
                    <td style="padding: 10px 8px; text-align: right; font-size: 10px; color: #000; border-right: 1px solid #e5e7eb;">{{ money($item->unit_price) }}</td>
                    <td style="padding: 10px 8px; text-align: right; font-size: 10px; color: #000; font-weight: 700;">{{ money($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals Section --}}
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
            <div style="width: 300px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px 12px; font-size: 10px; color: #000; background: #f9fafb; font-weight: 600;">Subtotal</td>
                        <td style="padding: 8px 12px; text-align: right; font-size: 10px; color: #000; font-weight: 600; background: #fff;">{{ money($order->subtotal) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px 12px; font-size: 10px; color: #000; background: #f9fafb; font-weight: 600;">Shipping Charge<br><span style="font-size: 8px; color: #666; font-weight: 400;">({{ $order->delivery_zone->label() }})</span></td>
                        <td style="padding: 8px 12px; text-align: right; font-size: 10px; color: #000; font-weight: 600; background: #fff;">{{ money($order->shipping_cost) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px 12px; font-size: 10px; color: #059669; background: #f9fafb; font-weight: 600;">Discount @if($order->coupon)<br><span style="font-size: 8px; font-weight: 400;">({{ $order->coupon->code }})</span>@endif</td>
                        <td style="padding: 8px 12px; text-align: right; font-size: 10px; color: #059669; font-weight: 600; background: #fff;">-{{ money($order->discount_amount) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #000;">
                        <td style="padding: 12px; font-size: 11px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">Grand Total</td>
                        <td style="padding: 12px; text-align: right; font-size: 14px; font-weight: 700; color: #fff;">{{ money($order->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Shipping & Tracking Information --}}
        @if($order->tracking_number)
        <div style="margin-bottom: 15px; border-left: 3px solid #000; padding: 10px 0 10px 10px;">
            <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">SHIPPING & TRACKING</h3>
            <table style="width: 100%; font-size: 10px; line-height: 1.7;">
                <tr>
                    <td style="padding: 1px 0; color: #666; width: 20%; font-weight: 600;">Courier Service:</td>
                    <td style="padding: 1px 0; color: #000; font-weight: 700;">{{ $order->courier }}</td>
                </tr>
                <tr>
                    <td style="padding: 1px 0; color: #666; vertical-align: top; font-weight: 600;">Tracking Number:</td>
                    <td style="padding: 1px 0; color: #000; font-family: 'Courier New', monospace; font-size: 9px; font-weight: 700;">{{ $order->tracking_number }}</td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Customer Notes --}}
        @if($order->notes)
        <div style="margin-bottom: 15px; border-left: 3px solid #f59e0b; padding: 10px 0 10px 10px; background: #fffbeb;">
            <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">CUSTOMER NOTES</h3>
            <p style="margin: 0; font-size: 10px; color: #000; line-height: 1.6;">{{ $order->notes }}</p>
        </div>
        @endif

        {{-- Terms & Footer --}}
        <div style="border-top: 2px solid #e5e7eb; padding-top: 12px; margin-top: 15px;">
            <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">TERMS & CONDITIONS</h3>
            <p style="margin: 0 0 12px 0; font-size: 8px; color: #666; line-height: 1.6;">
                • Payment is due within 15 days from the invoice date. Please include the invoice number with your payment.<br>
                • All sales are final. Returns or exchanges are only accepted for defective products within 7 days of delivery.<br>
                • For any queries regarding this invoice, please contact us at +880 1711-123456 or info@spinnerfashion.com
            </p>
            <div style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                <p style="margin: 0 0 4px 0; font-size: 10px; color: #000; font-weight: 700;">Thank you for shopping with Spinner Fashion!</p>
                <p style="margin: 0; font-size: 8px; color: #666;">
                    For support & inquiries: +880 1711-123456 | info@spinnerfashion.com | www.spinnerfashion.com
                </p>
            </div>
        </div>
    </div>
</div>
{{-- End Invoice Template --}}

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Order?</h3>
            <p class="text-gray-600">Are you sure you want to delete this order? This action cannot be undone.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium">
                Cancel
            </button>
            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{{-- html2pdf.js library for PDF generation --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadInvoice() {
        const invoiceElement = document.getElementById('invoiceTemplate');
        const invoiceNumber = '{{ $order->order_number }}';

        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';

        // Clone the element to avoid modifying the original
        const clonedElement = invoiceElement.cloneNode(true);
        clonedElement.classList.remove('hidden');

        // Configure pdf options
        const opt = {
            margin: 0,
            filename: `Invoice-${invoiceNumber}.pdf`,
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true,
                scrollY: 0,
                scrollX: 0
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait',
                compress: true
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy']
            }
        };

        // Generate and download PDF
        html2pdf().set(opt).from(clonedElement).save().then(() => {
            // Restore button state
            button.disabled = false;
            button.innerHTML = originalText;
        }).catch((error) => {
            console.error('PDF generation error:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            alert('Failed to generate PDF. Please try again.');
        });
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection