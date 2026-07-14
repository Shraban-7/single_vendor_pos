@extends('admin.layouts.app')
@section('title', 'New Purchase')

@section('content')
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-bold tracking-tight text-slate-900">New Purchase</h1>
    <a href="{{ route('admin.purchases.index') }}" class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back
    </a>
</div>

<form action="{{ route('admin.purchases.store') }}" method="POST" x-data="purchaseForm({{ json_encode($products) }})" class="space-y-4">
    @csrf
    <div class="grid lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            {{-- Items Section --}}
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5 mb-3">Purchase Items</h2>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-end p-3 bg-slate-50/50 rounded-lg border border-slate-100">
                            <div class="col-span-5">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Product</label>
                                <select x-model="item.product_id" @change="updatePrice(index)" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                                    <option value="">Select Product</option>
                                    <template x-for="prod in products" :key="prod.id">
                                        <option :value="prod.id" x-text="prod.name + ' (' + (prod.unit ? prod.unit.short_name : '') + ')'"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Qty</label>
                                <input type="number" step="0.01" x-model.number="item.quantity" @input="calculateTotal(index)" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Unit Price</label>
                                <input type="number" step="0.01" x-model.number="item.unit_price" @input="calculateTotal(index)" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-2 flex items-center justify-between">
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400">Total</p>
                                    <p class="text-sm font-bold text-slate-800" x-text="formatMoney(item.quantity * item.unit_price)"></p>
                                </div>
                                <button type="button" @click="removeItem(index)" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded transition" x-show="items.length > 1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addItem()" class="mt-3 w-full py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Item
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Details</h2>
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 mb-1">Supplier *</label>
                    <select name="supplier_id" required class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }} {{ $sup->company_name ? '('.$sup->company_name.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Date *</label>
                        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Due Date</label>
                        <input type="date" name="due_date" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Payment</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between font-semibold text-slate-700">
                        <span>Subtotal:</span>
                        <span x-text="formatMoney(grandTotal)"></span>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Paid Amount</label>
                        <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" @input="calculateDue" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div class="flex justify-between font-bold text-rose-600">
                        <span>Due:</span>
                        <span x-text="formatMoney(dueAmount)"></span>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full h-9 text-xs border border-slate-200 rounded-lg px-2 focus:ring-1 focus:ring-indigo-500">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full h-10 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm flex items-center justify-center gap-1.5">
                <i data-lucide="save" class="w-4 h-4"></i> Save Purchase
            </button>
        </div>
    </div>

    {{-- Hidden Inputs for Alpine to submit --}}
    <template x-for="(item, index) in items" :key="index">
        <div>
            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
            <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
            <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.unit_price">
        </div>
    </template>
</form>

@push('scripts')
<script>
    function purchaseForm(products) {
        return {
            products: products,
            items: [{ product_id: '', quantity: 1, unit_price: 0 }],
            paidAmount: 0,
            get grandTotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            },
            get dueAmount() {
                return Math.max(0, this.grandTotal - this.paidAmount);
            },
            addItem() {
                this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
            },
            removeItem(index) {
                this.items.splice(index, 1);
            },
            updatePrice(index) {
                const prod = this.products.find(p => p.id == this.items[index].product_id);
                if (prod) {
                    this.items[index].unit_price = prod.cost_price || 0;
                }
            },
            calculateTotal(index) {
                // Reactive via getters, but triggered to ensure UI updates
            },
            calculateDue() {
                // Reactive via getters
            },
            formatMoney(amount) {
                return '৳ ' + parseFloat(amount || 0).toFixed(2);
            }
        }
    }
</script>
@endpush
@endsection
