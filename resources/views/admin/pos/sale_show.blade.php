@extends('admin.layouts.app')

@section('title', 'Sale #' . $order->order_number)

@section('content')
{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Sale {{ $order->order_number }}</h1>
            <p class="text-xs text-slate-500">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if ($order->status->value !== 'draft')
        <button onclick="printReceipt('{{ route('admin.pos.receipt', $order->order_number) }}')"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-amber-600 rounded-lg shadow-sm hover:bg-amber-700 transition">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
            <span>Receipt</span>
        </button>
        <button onclick="printReceipt('{{ route('admin.orders.invoice', $order->order_number) }}')"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
            <span>Invoice</span>
        </button>
        <button onclick="openReturnModal()"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-violet-600 rounded-lg shadow-sm hover:bg-violet-700 transition">
            <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
            <span>Return</span>
        </button>
        @endif
        @if($order->status->value !== 'cancelled')
        <button onclick="openDeleteModal()"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-white bg-rose-600 rounded-lg shadow-sm hover:bg-rose-700 transition">
            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
            <span>Delete</span>
        </button>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left Column - Order Details --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Order Status --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400"></i>
                Order Status
            </h2>

            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="text-xs space-y-3">
                @csrf
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Status</label>
                        <select name="status" class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                            <option value="pending" {{ $order->status->value === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status->value === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ $order->status->value === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status->value === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status->value === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Comment (Optional)</label>
                        <input type="text" name="comment" placeholder="Add a note..." class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    </div>
                </div>
                <button type="submit" class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>Update Status
                </button>
            </form>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5 text-slate-400"></i>
                Order Items ({{ $order->items->count() }})
            </h2>

            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex gap-3 relative">

                    @if(!empty($item->return_item_id))
                    <span class="absolute top-0 right-0 px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-700 border border-rose-100/70">Returned</span>
                    @endif

                    <div class="w-16 h-16 shrink-0 rounded-lg overflow-hidden bg-slate-50 border border-slate-100">
                        <img src="{{ $item->product?->thumbnail }}"
                            alt="{{ $item->product_name }}"
                            class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-slate-800 text-sm mb-0.5">{{ $item->product_name }}</h4>

                        @if($item->size_name || $item->color_name)
                        <p class="text-[11px] text-slate-500 mb-1">
                            @if($item->size_name)Size: {{ $item->size_name }}@endif
                            @if($item->size_name && $item->color_name) | @endif
                            @if($item->color_name)Color: {{ $item->color_name }}@endif
                        </p>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">
                                Qty: {{ $item->quantity }} × {{ money($item->unit_price) }}
                            </span>
                            <span class="text-sm font-bold text-slate-900">
                                {{ money($item->total) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal</span>
                    <span class="font-medium text-slate-800">{{ money($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Shipping Cost</span>
                    <span class="font-medium text-slate-800">{{ money($order->shipping_cost) }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span>
                        <span class="font-medium">-{{ money($order->discount_amount) }}</span>
                    </div>
                @endif

                @if($totalRefund > 0)
                    <div class="flex justify-between text-rose-600">
                        <span>Total Refund</span>
                        <span class="font-medium">-{{ money($totalRefund) }}</span>
                    </div>
                    <div class="space-y-0.5 pl-2">
                        @foreach($refunds as $method => $amount)
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span class="capitalize">{{ $method }}</span>
                                <span>-{{ money($amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="h-px bg-slate-200"></div>

                <div class="flex justify-between text-sm">
                    <span class="font-bold text-slate-900">Total</span>
                    <span class="text-lg font-bold text-slate-900">{{ money($order->total) }}</span>
                </div>

                @if($totalRefund > 0)
                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Net Paid</span>
                        <span class="font-semibold">{{ money($order->total - $totalRefund) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Status History --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="history" class="w-3.5 h-3.5 text-slate-400"></i>
                Status History
            </h2>

            <div class="space-y-3">
                @forelse($order->statusHistories as $history)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="circle" class="w-2.5 h-2.5 text-slate-500 fill-slate-500"></i>
                        </div>
                        @if(!$loop->last)
                        <div class="flex-1 w-0.5 bg-slate-200 my-1"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-3">
                        <div class="flex items-start justify-between gap-4 mb-0.5">
                            <span class="font-semibold text-slate-800 text-xs">{{ $history->status->label() }}</span>
                            <span class="text-[10px] text-slate-400">{{ $history->created_at->diffForHumans() }}</span>
                        </div>
                        @if($history->comment)
                        <p class="text-[11px] text-slate-600 mb-0.5">{{ $history->comment }}</p>
                        @endif
                        @if($history->updater)
                        <p class="text-[10px] text-slate-400">By: {{ $history->updater->name }}</p>
                        @endif
                        <p class="text-[10px] text-slate-400">{{ $history->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500 text-center py-3">No status history available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column - Customer & Shipping Info --}}
    <div class="space-y-4">
        {{-- Customer Information --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                Customer Information
            </h2>

            <div class="space-y-2.5 text-xs">
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Name</p>
                    <p class="font-semibold text-slate-800">{{ $order->shipping_name }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Phone</p>
                    <a href="tel:{{ $order->shipping_phone }}" class="font-semibold text-indigo-600 hover:text-indigo-800">
                        {{ $order->shipping_phone }}
                    </a>
                </div>
                @if($order->shipping_email)
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Email</p>
                    <a href="mailto:{{ $order->shipping_email }}" class="font-semibold text-indigo-600 hover:text-indigo-800">
                        {{ $order->shipping_email }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                Shipping Address
            </h2>

            @if($order->shipping_address)
            <div class="space-y-2.5 text-xs">
                <div>
                    <p class="text-slate-800">{{ $order->shipping_address }}</p>
                    <p class="text-slate-500">{{ $order->shipping_city }}, {{ $order->shipping_district }}</p>
                </div>
                <div class="pt-2.5 border-t border-slate-100">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[11px] font-medium rounded bg-indigo-50 text-indigo-700 border border-indigo-100/70">
                        <i data-lucide="truck" class="w-3 h-3"></i>
                        {{ $order->delivery_zone ? $order->delivery_zone->label() : '' }}
                    </span>
                </div>
            </div>
            @endif
        </div>

        {{-- Payment Information --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="credit-card" class="w-3.5 h-3.5 text-slate-400"></i>
                Payment Information
            </h2>

            <div class="space-y-2.5 text-xs">
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Payment Method</p>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium rounded bg-slate-100 text-slate-700">
                        {{ $order->payment_method->label() }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Payment Status</p>
                    @if($order->payment_status->value === 'paid')
                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Paid</span>
                    @else
                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 border border-amber-200/60">{{ $order->payment_status->label() }}</span>
                    @endif
                </div>
                @if($order->transaction_id)
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Transaction ID</p>
                    <code class="block bg-slate-50 px-2.5 py-1.5 rounded-lg text-[11px] font-mono text-slate-700">{{ $order->transaction_id }}</code>
                </div>
                @endif
                @if($order->paid_at)
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Paid At</p>
                    <p class="text-xs text-slate-800">{{ $order->paid_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Tracking Information --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="truck" class="w-3.5 h-3.5 text-slate-400"></i>
                Tracking Information
            </h2>

            @if($order->tracking_number)
            <div class="space-y-2.5 mb-3 text-xs">
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Courier</p>
                    <p class="font-semibold text-slate-800">{{ $order->courier }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 mb-0.5">Tracking Number</p>
                    <code class="block bg-slate-50 px-2.5 py-1.5 rounded-lg text-[11px] font-mono text-slate-700">{{ $order->tracking_number }}</code>
                </div>
            </div>
            @endif

            <form action="{{ route('admin.orders.update-tracking', $order->id) }}" method="POST" class="text-xs space-y-2.5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Courier</label>
                    <input type="text" name="courier" value="{{ old('courier', $order->courier) }}" placeholder="e.g., Sundarban, Pathao" class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Enter tracking number" class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                </div>
                <button type="submit" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>Save Tracking Info
                </button>
            </form>
        </div>

        {{-- Employee Information --}}
        @if ($order->is_pos == 1)
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                Employee Information
            </h2>
            @if($order->employee)
            <div class="text-xs">
                <p class="font-semibold text-slate-800">{{ $order->employee->name ?? '' }}</p>
            </div>
            @else
            <p class="text-xs text-slate-500">No employee.</p>
            @endif
        </div>
        @endif

        {{-- Admin Notes --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="sticky-note" class="w-3.5 h-3.5 text-slate-400"></i>
                Admin Notes
            </h2>

            <form action="{{ route('admin.orders.update-notes', $order->id) }}" method="POST" class="text-xs space-y-2.5">
                @csrf
                <textarea name="admin_notes" rows="3" placeholder="Add internal notes..." class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 resize-none">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                <button type="submit" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>Save Notes
                </button>
            </form>
        </div>

        @if($order->notes)
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 mb-3 flex items-center gap-1.5">
                <i data-lucide="message-square" class="w-3.5 h-3.5 text-slate-400"></i>
                Customer Notes
            </h2>
            <p class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-lg">{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>


{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl">
        <div class="text-center mb-5">
            <div class="w-12 h-12 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-1">Delete Order?</h3>
            <p class="text-xs text-slate-500">Are you sure you want to delete this order? This action cannot be undone.</p>
        </div>
        <div class="flex gap-2.5">
            <button onclick="closeDeleteModal()" class="flex-1 h-9 inline-flex items-center justify-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                Cancel
            </button>
            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <input type="hidden" name="source" value="{{ $source }}">
                <button type="submit" class="w-full h-9 inline-flex items-center justify-center text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div id="returnModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg p-5">

        <div class="flex justify-between items-center border-b border-slate-200 pb-3 mb-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Sale Return</h2>
            <button onclick="closeReturnModal()" class="text-slate-400 hover:text-rose-600 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="max-h-72 overflow-y-auto space-y-2 text-xs">
            @foreach($order->items as $item)
            <div class="border border-slate-200 p-2.5 rounded-lg flex gap-3 items-start bg-slate-50/30">
                <input type="checkbox"
                    class="return-check mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-0 {{ $item->return_item_id ? 'hidden' : '' }}"
                    data-id="{{ $item->id }}"
                    data-max="{{ $item->quantity }}"
                    data-default-price="{{ $item->unit_price }}">

                <div class="flex-1">
                    <p class="font-semibold text-slate-800 text-sm">{{ $item->product->name }}</p>
                    <p class="text-[10px] text-slate-500">Qty: {{ $item->quantity }} | Price: {{ $item->unit_price }}</p>

                    <div id="edit-{{ $item->id }}" class="hidden mt-2 flex gap-2">
                        <input type="number"
                            class="qty-input w-20 h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white"
                            min="1"
                            max="{{ $item->quantity }}"
                            value="{{ $item->quantity }}">
                        <input type="number"
                            class="price-input w-24 h-8 px-2 text-xs border border-slate-200 rounded-lg bg-white"
                            value="{{ $item->unit_price }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 text-xs space-y-2.5">
            <div class="flex justify-between">
                <span class="text-slate-500">Calculated Refund:</span>
                <span class="font-bold text-emerald-600">৳<span id="calculatedRefund">0.00</span></span>
            </div>
            <input type="number" id="refundAmount" placeholder="Refund Amount"
                class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg bg-slate-50/50">
            <select id="refundMethod"
                class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg bg-slate-50/50">
                <option value="cash">Cash</option>
                <option value="bkash">bKash</option>
                <option value="bank">Bank</option>
                <option value="card">Card</option>
            </select>
            <textarea id="refundRemarks" placeholder="Remarks"
                class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg bg-slate-50/50"></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-100">
            <button onclick="closeReturnModal()"
                class="h-9 px-3.5 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                Cancel
            </button>
            <button onclick="openConfirmReturnModal()"
                class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition shadow-sm">
                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i> Process Return
            </button>
        </div>
    </div>
</div>

<div id="confirmReturnModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-5">

        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Confirm Return</h3>

        <div id="confirmDetails" class="text-xs text-slate-600 space-y-2 max-h-60 overflow-y-auto"></div>

        <div class="border-t border-slate-200 mt-3 pt-3 text-xs space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500">Total Refund:</span>
                <span class="font-bold text-emerald-600">৳<span id="confirmRefund">0.00</span></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Method:</span>
                <span id="confirmMethod" class="font-medium text-slate-800"></span>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-100">
            <button onclick="closeConfirmReturnModal()"
                class="h-9 px-3.5 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                Cancel
            </button>
            <button onclick="confirmReturnAction()"
                class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition shadow-sm">
                <i data-lucide="check" class="w-3.5 h-3.5"></i> Yes, Proceed
            </button>
        </div>
    </div>
</div>

@push('scripts')
{{-- html2pdf.js library for PDF generation --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function printReceipt(url) {
        let printWindow = window.open(url, '_blank', 'width=800,height=600');
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close();
            };
        };
    }

    function openReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
        setTimeout(() => { calculateRefund(); }, 100);
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }

    $(document).on("change input", ".return-check, .qty-input, .price-input", function() {
        calculateRefund();
    });

    function calculateRefund() {
        let total = 0;
        $(".return-check:checked").each(function() {
            let id = $(this).data("id");
            let row = $("#edit-" + id);
            let qty = parseFloat(row.find(".qty-input").val()) || 0;
            let price = parseFloat(row.find(".price-input").val()) || 0;
            total += qty * price;
        });
        $("#calculatedRefund").text(total.toFixed(2));
        $("#refundAmount").val(total.toFixed(2));
    }

    function openConfirmReturnModal() {
        $("#returnModal").addClass("hidden");
        let itemsHTML = '';
        let total = 0;
        $(".return-check:checked").each(function() {
            let id = $(this).data("id");
            let row = $("#edit-" + id);
            let name = row.closest('.flex').find('p').first().text();
            let qty = parseFloat(row.find(".qty-input").val()) || 0;
            let price = parseFloat(row.find(".price-input").val()) || 0;
            let lineTotal = qty * price;
            total += lineTotal;
            itemsHTML += `<div class="flex justify-between border-b border-slate-100 pb-1"><span class="truncate">${name} (x${qty})</span><span>৳${lineTotal.toFixed(2)}</span></div>`;
        });
        if (!itemsHTML) { alert("Select at least one item"); return; }
        let refundInput = parseFloat($("#refundAmount").val()) || 0;
        if (refundInput <= 0) { alert("Invalid refund amount"); return; }
        let method = $("#refundMethod").val() || 'N/A';
        $("#confirmDetails").html(itemsHTML);
        $("#confirmRefund").text(total.toFixed(2));
        $("#confirmMethod").text(method.toUpperCase());
        setTimeout(() => { $("#confirmReturnModal").removeClass("hidden"); }, 150);
    }

    function closeConfirmReturnModal() {
        $("#confirmReturnModal").addClass("hidden");
    }

    let isProcessingReturn = false;

    function confirmReturnAction() {
        if (isProcessingReturn) return;
        isProcessingReturn = true;
        closeConfirmReturnModal();
        submitReturn();
    }

    function submitReturn() {
        let items = [];
        $(".return-check:checked").each(function() {
            let id = $(this).data("id");
            let row = $("#edit-" + id);
            let quantity = parseInt(row.find(".qty-input").val());
            let unit_price = parseFloat(row.find(".price-input").val());
            if (!quantity || quantity <= 0) return;
            items.push({ id: id, quantity: quantity, unit_price: unit_price });
        });
        if (items.length === 0) { alert("Select at least one item"); isProcessingReturn = false; return; }
        let payload = {
            items: items,
            refund_amount: parseFloat($("#refundAmount").val()) || 0,
            refund_method: $("#refundMethod").val(),
            remarks: $("#refundRemarks").val(),
        };
        $.ajax({
            url: `/admin/orders/{{ $order->id }}/return`,
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success) {
                    window.showSuccess(res.message || "Return processed successfully");
                    setTimeout(() => { location.reload(); }, 800);
                } else {
                    window.showError(res.message || "Failed");
                }
            },
            error: function(xhr) {
                let message = "Something went wrong";
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join("\n");
                }
                window.showError(message);
            },
            complete: function() { isProcessingReturn = false; }
        });
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush
@endsection
