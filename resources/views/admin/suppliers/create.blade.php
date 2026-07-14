@extends('admin.layouts.app')
@section('title', 'Add Supplier')

@section('content')
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Add New Supplier</h1>
        <p class="text-xs text-slate-500">Register a new vendor to your supply chain network.</p>
    </div>
    <div>
        <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to List</span>
        </a>
    </div>
</div>

<form action="{{ route('admin.suppliers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    <div class="grid lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Basic Information</h2>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="name" label="Supplier Name *" required placeholder="e.g. ABC Traders" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="company_name" label="Company Name" placeholder="e.g. ABC Traders Ltd." class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="product_category" label="Product Category" placeholder="e.g. Electronics, Groceries" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="supplier_code" label="Supplier Code" placeholder="Auto-generated if empty" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Contact Details</h2>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="phone" label="Primary Phone" placeholder="+8801XXXXXXXXX" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="phone_secondary" label="Secondary Phone" placeholder="+8801XXXXXXXXX" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div>
                        <x-input name="email" label="Email Address" type="email" placeholder="supplier@example.com" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>
                    <div class="md:col-span-2">
                        <x-textarea name="address" label="Address" rows="2" placeholder="Full physical address" class="text-xs bg-slate-50/50 focus:bg-white" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Financial & Notes</h2>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <x-input name="opening_balance" label="Opening Balance (৳)" type="number" step="0.01" value="0.00" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <p class="mt-1 text-[10px] text-slate-400">Initial due amount or credit balance</p>
                    </div>
                    <div class="md:col-span-2">
                        <x-textarea name="notes" label="Internal Notes" rows="3" placeholder="Any specific terms, conditions, or notes about this supplier" class="text-xs bg-slate-50/50 focus:bg-white" />
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Settings</h2>
                <label class="inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-200 text-indigo-600 focus:ring-0 w-3.5 h-3.5 shadow-inner">
                    <span class="ml-2 font-medium text-slate-700">Active Status</span>
                </label>
            </div>

            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1.5">Profile Image</h2>
                <div class="border border-dashed border-slate-200 bg-slate-50/50 rounded-xl p-4 text-center hover:border-slate-400 transition relative">
                    <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <div id="imagePlaceholder" class="py-2">
                        <i data-lucide="user" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                        <p class="text-slate-600 font-semibold mb-0.5">Click to upload avatar</p>
                        <p class="text-[10px] text-slate-400">PNG, JPG up to 2MB</p>
                    </div>
                    <div id="imagePreview" class="hidden relative">
                        <img src="" class="mx-auto h-24 w-24 object-cover rounded-full border border-slate-200">
                        <button type="button" onclick="resetImage()" class="absolute top-0 right-0 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 shadow transition">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </button>
                    </div>
                    <button type="button" onclick="document.getElementById('image').click()" class="mt-2.5 px-3 h-7 inline-flex items-center justify-center text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100/70 rounded-md hover:bg-indigo-100/60 transition">
                        Choose File
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm flex flex-col gap-2">
                <button type="submit" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Save Supplier</span>
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="w-full h-9 inline-flex items-center justify-center text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition text-center">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#imagePreview img').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('imagePlaceholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
    function resetImage() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('imagePlaceholder').classList.remove('hidden');
    }
</script>
@endpush
@endsection
