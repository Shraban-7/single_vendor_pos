@extends('admin.layouts.app')

@section('title', 'Sale #' . $sale->invoice_number)

@section('content')
{{-- Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.ecommerce-sales.index') }}" class="w-10 h-10 flex items-center justify-center border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">Sale {{ $sale->invoice_number }}</h1>
                @php
                    $statusColors = [
                        'draft' => 'bg-amber-50 text-amber-700 border-amber-100',
                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                    ];
                    $statusValue = is_object($sale->status) ? $sale->status->value : $sale->status;
                @endphp
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $statusColors[$statusValue] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                    {{ is_object($sale->status) ? $sale->status->label() : ucfirst($statusValue) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Created on {{ $sale->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button onclick="downloadInvoice()" class="flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-medium bg-white border border-slate-200 text-slate-700 rounded-lg shadow-sm hover:bg-slate-50 transition">
            <i data-lucide="download" class="w-3.5 h-3.5"></i>
            <span>PDF</span>
        </button>

        @if($statusValue !== 'cancelled' && (!$sale->has_return || $sale->return_status !== 'full'))
        <button onclick="openReturnModal()" class="flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
            <span>Return / Exchange</span>
        </button>
        @endif

        @if($statusValue !== 'cancelled')
        <button onclick="openDeleteModal()" class="flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-medium text-rose-600 bg-rose-50 border border-rose-100 rounded-lg hover:bg-rose-100 transition">
            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
        </button>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left Column - Order Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Order Items --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-indigo-600"></i>
                    Order Items ({{ $sale->items->count() }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/70 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3 text-center">Qty</th>
                            <th class="px-5 py-3 text-right">Unit Price</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sale->items as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-800">{{ $item->product_name }}</p>
                                @if(!empty($item->product_variant_id) || !empty($item->size_name) || !empty($item->color_name))
                                <p class="text-[10px] text-slate-500 mt-0.5">
                                    {{ trim(($item->size_name ?? '') . ' - ' . ($item->color_name ?? ''), ' -') }}
                                </p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-600">{{ number_format($item->quantity, 2) }}</td>
                            <td class="px-5 py-3.5 text-right text-slate-600">{{ money($item->unit_price) }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-900">{{ money($item->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Order Summary --}}
            <div class="px-5 py-4 bg-slate-50/30 border-t border-slate-100">
                <div class="max-w-xs ml-auto space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-900">{{ money($sale->subtotal) }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount</span>
                        <span class="font-medium">-{{ money($sale->discount_amount) }}</span>
                    </div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax ({{ $sale->vat_rate }}%)</span>
                        <span class="font-medium text-slate-900">{{ money($sale->tax_amount) }}</span>
                    </div>
                    @endif
                    <div class="h-px bg-slate-200 my-2"></div>
                    <div class="flex justify-between text-base">
                        <span class="font-bold text-slate-900">Total Amount</span>
                        <span class="font-bold text-indigo-600">{{ money($sale->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Paid</span>
                        <span class="font-medium text-emerald-600">{{ money($sale->paid_amount) }}</span>
                    </div>
                    @if($sale->due_amount > 0)
                    <div class="flex justify-between text-rose-600 font-semibold">
                        <span>Due</span>
                        <span>{{ money($sale->due_amount) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status History --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-indigo-600"></i>
                Status History
            </h2>

            <div class="space-y-0">
                @forelse($sale->statusHistories ?? [] as $history)
                <div class="flex gap-4 relative">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center flex-shrink-0 border border-indigo-100 z-10">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-indigo-600"></i>
                        </div>
                        @if(!$loop->last)
                        <div class="flex-1 w-0.5 bg-slate-200 my-1"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-6">
                        <div class="flex items-start justify-between gap-4 mb-1">
                            <span class="text-sm font-semibold text-slate-800">{{ is_object($history->status) ? $history->status->label() : ucfirst($history->status) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $history->created_at->format('M d, h:i A') }}</span>
                        </div>
                        @if(!empty($history->comment))
                        <p class="text-xs text-slate-600 mb-1">{{ $history->comment }}</p>
                        @endif
                        @if(!empty($history->updater))
                        <p class="text-[10px] text-slate-400">By: {{ $history->updater->name }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">No status history available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column - Customer & Payment Info --}}
    <div class="space-y-6">
        {{-- Customer Information --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i>
                Customer Information
            </h2>

            <div class="space-y-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Name</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Phone</p>
                    <a href="tel:{{ $sale->customer->phone ?? '' }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                        {{ $sale->customer->phone ?? '—' }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="credit-card" class="w-4 h-4 text-indigo-600"></i>
                Payment Information
            </h2>

            <div class="space-y-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Method</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-md text-xs font-medium text-slate-700 capitalize">
                        {{ str_replace('_', ' ', $sale->payment_method ?? '—') }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</p>
                    @php
                        $pStatus = is_object($sale->payment_status) ? $sale->payment_status->value : $sale->payment_status;
                    @endphp
                    @if($pStatus === 'paid')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-xs font-medium border border-emerald-100">
                        <i data-lucide="check-circle" class="w-3 h-3"></i> Paid
                    </span>
                    @elseif($pStatus === 'partial')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-xs font-medium border border-amber-100">
                        <i data-lucide="clock" class="w-3 h-3"></i> Partial
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-700 rounded-md text-xs font-medium border border-rose-100">
                        <i data-lucide="x-circle" class="w-3 h-3"></i> Unpaid
                    </span>
                    @endif
                </div>
                @if($sale->paid_at)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Paid At</p>
                    <p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($sale->paid_at)->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="sticky-note" class="w-4 h-4 text-indigo-600"></i>
                Internal Notes
            </h2>
            <form action="{{ route('admin.ecommerce-sales.update-notes', $sale->id) }}" method="POST">
                @csrf
                <textarea name="notes" rows="4" placeholder="Add internal notes about this sale..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none mb-3">{{ old('notes', $sale->notes) }}</textarea>
                <button type="submit" class="w-full px-4 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i> Save Notes
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- RETURN / EXCHANGE MODAL --}}
{{-- ========================================== --}}
<div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" onclick="closeReturnModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block w-full max-w-2xl p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-indigo-600"></i>
                    Process Return / Exchange
                </h3>
                <button type="button" onclick="closeReturnModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.saleReturns.store') }}" method="POST" x-data="returnForm({{ json_encode($sale->items->map(fn($i) => [
                'id' => $i->id,
                'product_id' => $i->product_id,
                'name' => $i->product_name,
                'quantity' => $i->quantity,
                'quantity_returned' => $i->quantity_returned ?? 0,
                'unit_price' => $i->unit_price
            ])) }})" class="space-y-4">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <input type="hidden" name="return_date" value="{{ \Carbon\Carbon::now()->toDateString() }}">

                {{-- Tab Switcher --}}
                <div class="flex gap-1 p-1 bg-slate-100 rounded-lg">
                    <button type="button" @click="activeTab = 'return'" :class="activeTab === 'return' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="flex-1 h-8 text-xs font-semibold rounded-md transition">Return</button>
                    <button type="button" @click="activeTab = 'exchange'" :class="activeTab === 'exchange' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="flex-1 h-8 text-xs font-semibold rounded-md transition">Exchange</button>
                </div>

                {{-- RETURN TAB --}}
                <div x-show="activeTab === 'return'" class="space-y-4">
                    {{-- Items to Return --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-3">Select Items to Return</h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(item, index) in returnItems" :key="index">
                                <div x-show="item.quantity > item.quantity_returned" class="flex items-center gap-3 p-3 bg-white rounded-lg border border-slate-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate" x-text="item.name"></p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                            Purchased: <span x-text="item.quantity"></span> |
                                            Returned: <span x-text="item.quantity_returned"></span> |
                                            Available: <span x-text="item.quantity - item.quantity_returned" class="font-bold text-indigo-600"></span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <label class="block text-[9px] font-semibold text-slate-500 mb-1">Qty</label>
                                            <input type="number" step="0.01" x-model.number="item.return_qty" :max="item.quantity - item.quantity_returned" min="0" @input="calculateTotal()" class="w-16 h-8 text-xs border border-slate-300 rounded-md px-1 text-center focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex flex-col items-center justify-center pt-4">
                                            <label class="inline-flex items-center cursor-pointer select-none">
                                                <input type="checkbox" x-model="item.stock_replaced" class="rounded border-slate-300 text-indigo-600 focus:ring-0 w-3.5 h-3.5">
                                                <span class="ml-1 text-[9px] font-medium text-slate-600">Restock</span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Hidden inputs for submission -->
                                    <input type="hidden" :name="'items['+index+'][sale_item_id]'" :value="item.id">
                                    <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                    <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.return_qty">
                                    <input type="hidden" :name="'items['+index+'][stock_replaced]'" :value="item.stock_replaced">
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Return Type</label>
                            <select name="return_type" x-model="returnType" class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500">
                                <option value="partial">Partial Return</option>
                                <option value="full">Full Return</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Reason</label>
                            <select name="reason" required class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500">
                                <option value="defective">Defective / Damaged</option>
                                <option value="wrong_item">Wrong Item Delivered</option>
                                <option value="customer_changed_mind">Customer Changed Mind</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Refund Method</label>
                            <select name="refund_method" class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="store_credit">Store Credit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Refund Amount (৳)</label>
                            <input type="number" step="0.01" name="refund_amount" x-model.number="totalRefund" class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500 font-semibold text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Notes (Optional)</label>
                        <textarea name="reason_notes" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-indigo-500 resize-none" placeholder="Add specific details about this return..."></textarea>
                    </div>
                </div>

                {{-- EXCHANGE TAB --}}
                <div x-show="activeTab === 'exchange'" class="space-y-4">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-3">Add Exchange Products</h4>

                        {{-- Search --}}
                        <div class="relative mb-3">
                            <input type="text" x-model="exchangeSearch" @input.debounce.300ms="searchExchangeProducts()" placeholder="Search product by name or SKU..." class="w-full pl-8 pr-3 h-9 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-indigo-500 bg-white">
                            <i data-lucide="search" class="absolute w-3.5 h-3.5 left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <div x-show="exchangeResults.length > 0" class="absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                                <template x-for="p in exchangeResults" :key="p.id">
                                    <button type="button" @click="addExchangeItem(p)" class="w-full text-left px-3 py-2 hover:bg-indigo-50 flex items-center justify-between gap-2 border-b border-slate-100 last:border-0">
                                        <span class="text-xs font-medium text-slate-800 truncate" x-text="p.name"></span>
                                        <span class="text-[10px] text-slate-400 shrink-0">৳<span x-text="p.price"></span> · <span x-text="p.stock"></span> in stock</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(item, index) in exchangeItems" :key="item.temp_id">
                                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-slate-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate" x-text="item.name"></p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Price: ৳<span x-text="item.unit_price"></span></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <label class="block text-[9px] font-semibold text-slate-500 mb-1">Qty</label>
                                            <input type="number" step="0.01" x-model.number="item.quantity" min="0.01" :max="item.stock" @input="calculateExchange()" class="w-16 h-8 text-xs border border-slate-300 rounded-md px-1 text-center focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <button type="button" @click="removeExchangeItem(item.temp_id)" class="text-rose-500 hover:text-rose-700 pt-4">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" :name="'exchange_items['+index+'][product_id]'" :value="item.product_id">
                                    <input type="hidden" :name="'exchange_items['+index+'][quantity]'" :value="item.quantity">
                                    <input type="hidden" :name="'exchange_items['+index+'][unit_price]'" :value="item.unit_price">
                                </div>
                            </template>
                            <p x-show="exchangeItems.length === 0" class="text-[11px] text-slate-400 text-center py-4">No exchange products added yet.</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Refund Method</label>
                            <select name="refund_method" class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="store_credit">Store Credit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Exchange Value (৳)</label>
                            <input type="number" step="0.01" name="exchange_value" x-model.number="totalExchange" class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500 font-semibold text-slate-800">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Reason</label>
                            <select name="reason" required class="w-full h-9 text-xs border border-slate-300 rounded-lg px-3 focus:ring-1 focus:ring-indigo-500">
                                <option value="defective">Defective / Damaged</option>
                                <option value="wrong_item">Wrong Item Delivered</option>
                                <option value="customer_changed_mind">Customer Changed Mind</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-semibold text-slate-600 mb-1">Notes (Optional)</label>
                            <textarea name="reason_notes" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-indigo-500 resize-none" placeholder="Add specific details about this exchange..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="closeReturnModal()" class="px-4 h-9 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span x-text="activeTab === 'exchange' ? 'Process Exchange' : 'Process Return'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Invoice Template (Hidden) - A4 Size --}}
<div id="invoiceTemplate" class="hidden">
    <div class="invoice-container" style="width: 210mm; padding: 12mm 15mm; background: white; font-family: 'Arial', 'Helvetica', sans-serif; color: #000;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 3px solid #000;">
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; color: #000; margin: 0 0 3px 0; letter-spacing: -0.5px;">INVOICE</h1>
                <div style="width: 50px; height: 2px; background: #000; margin-bottom: 8px;"></div>
                <p style="font-size: 9px; color: #444; margin: 0; line-height: 1.6;">
                    NEXUS MART<br>
                    House 45, Road 12, Dhanmondi, Dhaka 1209<br>
                    Phone: +880 1712-345678 | support@nexusmart.com.bd
                </p>
            </div>
            <div style="text-align: right;">
                <table style="margin-left: auto; border-collapse: collapse; margin-top: 10px;">
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Invoice No:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ $sale->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Date:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ $sale->created_at->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 10px 3px 0; font-size: 9px; color: #666; text-align: right; font-weight: 600;">Status:</td>
                        <td style="padding: 3px 0; font-size: 9px; color: #000; font-weight: 700;">{{ strtoupper(is_object($sale->status) ? $sale->status->label() : $sale->status) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 30px; margin-bottom: 20px;">
            <div style="flex: 1; border-left: 3px solid #000; padding-left: 10px;">
                <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">BILL TO</h3>
                <p style="font-size: 10px; color: #000; margin: 0; line-height: 1.7;">
                    <strong style="font-size: 12px; display: block; margin-bottom: 5px; color: #000;">{{ $sale->customer->name ?? 'Walk-in Customer' }}</strong>
                    {{ $sale->customer->phone ?? '—' }}
                </p>
            </div>
            <div style="flex: 1; border-left: 3px solid #000; padding-left: 10px;">
                <h3 style="font-size: 9px; font-weight: 700; color: #000; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">PAYMENT</h3>
                <table style="width: 100%; font-size: 10px; line-height: 1.7;">
                    <tr>
                        <td style="color: #666; padding: 1px 0; width: 45%;">Method:</td>
                        <td style="color: #000; padding: 1px 0; font-weight: 600; text-transform: capitalize;">{{ str_replace('_', ' ', $sale->payment_method ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 1px 0;">Status:</td>
                        <td style="color: #000; padding: 1px 0; font-weight: 600;">{{ strtoupper(is_object($sale->payment_status) ? $sale->payment_status->label() : $sale->payment_status) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #000;">
                    <th style="padding: 10px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 5%;">SL</th>
                    <th style="padding: 10px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333;">Product</th>
                    <th style="padding: 10px 8px; text-align: center; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 8%;">Qty</th>
                    <th style="padding: 10px 8px; text-align: right; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; border-right: 1px solid #333; width: 15%;">Price</th>
                    <th style="padding: 10px 8px; text-align: right; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 8px; font-size: 9px; color: #666; border-right: 1px solid #e5e7eb; text-align: center;">{{ $index + 1 }}</td>
                    <td style="padding: 10px 8px; border-right: 1px solid #e5e7eb;">
                        <div style="font-size: 10px; font-weight: 600; color: #000; margin-bottom: 3px;">{{ $item->product_name }}</div>
                    </td>
                    <td style="padding: 10px 8px; text-align: center; font-size: 10px; color: #000; font-weight: 600; border-right: 1px solid #e5e7eb;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="padding: 10px 8px; text-align: right; font-size: 10px; color: #000; border-right: 1px solid #e5e7eb;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding: 10px 8px; text-align: right; font-size: 10px; color: #000; font-weight: 700;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
            <div style="width: 300px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px 12px; font-size: 10px; color: #000; background: #f9fafb; font-weight: 600;">Subtotal</td>
                        <td style="padding: 8px 12px; text-align: right; font-size: 10px; color: #000; font-weight: 600; background: #fff;">{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 8px 12px; font-size: 10px; color: #059669; background: #f9fafb; font-weight: 600;">Discount</td>
                        <td style="padding: 8px 12px; text-align: right; font-size: 10px; color: #059669; font-weight: 600; background: #fff;">-{{ number_format($sale->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #000;">
                        <td style="padding: 12px; font-size: 11px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px;">Grand Total</td>
                        <td style="padding: 12px; text-align: right; font-size: 14px; font-weight: 700; color: #fff;">{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="border-top: 2px solid #e5e7eb; padding-top: 12px; margin-top: 15px;">
            <p style="margin: 0; font-size: 8px; color: #666; line-height: 1.6;">
                • All sales are final. Returns or exchanges are only accepted for defective products within 7 days.<br>
                • For queries, contact: +880 1712-345678 | www.nexusmart.com.bd
            </p>
            <div style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                <p style="margin: 0 0 4px 0; font-size: 10px; color: #000; font-weight: 700;">Thank you for shopping with us!</p>
            </div>
        </div
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" onclick="closeDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all bg-white shadow-xl rounded-2xl">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Delete Sale?</h3>
                <p class="text-sm text-slate-500">Are you sure you want to delete this sale? This action cannot be undone and will affect inventory.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 h-10 text-sm font-medium text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                    Cancel
                </button>
                <form action="{{ route('admin.ecommerce-sales.destroy', $sale->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 h-10 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition">
                        Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Return Modal Alpine.js Logic
    function returnForm(initialItems) {
        return {
            activeTab: 'return',
            returnItems: initialItems.map(item => ({
                ...item,
                return_qty: item.quantity - item.quantity_returned,
                stock_replaced: true
            })),
            returnType: 'partial',
            totalRefund: 0,
            exchangeItems: [],
            exchangeSearch: '',
            exchangeResults: [],
            totalExchange: 0,
            calculateTotal() {
                this.totalRefund = this.returnItems.reduce((sum, item) => {
                    return sum + (item.return_qty * item.unit_price);
                }, 0);
            },
            calculateExchange() {
                this.totalExchange = this.exchangeItems.reduce((sum, item) => {
                    return sum + (item.quantity * item.unit_price);
                }, 0);
            },
            async searchExchangeProducts() {
                if (this.exchangeSearch.length < 1) {
                    this.exchangeResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/admin/sale-returns/search-products?term=${encodeURIComponent(this.exchangeSearch)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.exchangeResults = await res.json();
                } catch (e) {
                    this.exchangeResults = [];
                }
            },
            addExchangeItem(p) {
                if (this.exchangeItems.some(i => i.product_id === p.id)) {
                    this.exchangeSearch = '';
                    this.exchangeResults = [];
                    return;
                }
                this.exchangeItems.push({
                    temp_id: Date.now() + '_' + p.id,
                    product_id: p.id,
                    name: p.name,
                    unit_price: p.price,
                    stock: p.stock,
                    quantity: 1
                });
                this.exchangeSearch = '';
                this.exchangeResults = [];
                this.calculateExchange();
            },
            removeExchangeItem(tempId) {
                this.exchangeItems = this.exchangeItems.filter(i => i.temp_id !== tempId);
                this.calculateExchange();
            },
            init() {
                this.calculateTotal();
            }
        }
    }

    function openReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function downloadInvoice() {
        const invoiceElement = document.getElementById('invoiceTemplate');
        const invoiceNumber = '{{ $sale->invoice_number }}';
        const button = event.target.closest('button');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i> Generating...';
        if (window.lucide) lucide.createIcons();

        const clonedElement = invoiceElement.cloneNode(true);
        clonedElement.classList.remove('hidden');

        const opt = {
            margin: 0,
            filename: `Invoice-${invoiceNumber}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true }
        };

        html2pdf().set(opt).from(clonedElement).save().then(() => {
            button.disabled = false;
            button.innerHTML = originalText;
            if (window.lucide) lucide.createIcons();
        }).catch((error) => {
            console.error('PDF generation error:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            if (window.lucide) lucide.createIcons();
            alert('Failed to generate PDF. Please try again.');
        });
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeReturnModal();
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection
