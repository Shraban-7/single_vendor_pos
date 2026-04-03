@extends('customer.layout')
@section('title', 'Address')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Addresses</h2>
            <p class="text-gray-600 mt-1">Manage your saved shipping addresses</p>
        </div>
        <button onclick="openAddAddressModal()"
            class="bg-brand-blue text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">Add New Address</span>
            <span class="sm:hidden">Add</span>
        </button>
    </div>

    <!-- Addresses Grid -->
    @if($addresses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        @foreach($addresses as $address)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition relative">
            @if($address->is_default)
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-brand-blue-100 text-brand-blue-800 rounded-full text-xs font-semibold">
                    <i class="fas fa-star"></i>
                    Default
                </span>
            </div>
            @endif

            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-10 h-10 bg-brand-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-{{ $address->type->value === 'home' ? 'home' : 'building' }} text-brand-blue"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $address->name }}</h3>
                        <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium capitalize">
                            {{ $address->type->value }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-2 text-gray-600 text-sm mb-4">
                <p class="flex items-start gap-2">
                    <i class="fas fa-map-marker-alt mt-0.5 text-gray-400"></i>
                    <span>{{ $address->address_line_1 }}</span>
                </p>
                @if($address->address_line_2)
                <p class="ml-6">{{ $address->address_line_2 }}</p>
                @endif
                <p class="ml-6">
                    {{ $address->city }}
                    @if($address->state), {{ $address->state }}@endif
                    @if($address->postal_code) - {{ $address->postal_code }}@endif
                </p>
                <p class="flex items-center gap-2">
                    <i class="fas fa-phone text-gray-400"></i>
                    <span>{{ $address->phone }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 pt-4 border-t border-gray-200">
                <button onclick="editAddress({{ $address->id }})"
                    class="flex-1 bg-brand-blue-50 text-brand-blue px-4 py-2 rounded-lg font-medium hover:bg-brand-blue-100 transition text-sm">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <form action="{{ route('customer.addresses.delete', $address) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to delete this address?')"
                        class="w-full bg-red-50 text-red-600 px-4 py-2 rounded-lg font-medium hover:bg-red-100 transition text-sm">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
            <i class="fas fa-map-marker-alt text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No Addresses Saved</h3>
        <p class="text-gray-600 mb-6">Add your shipping address for faster checkout.</p>
        <button onclick="openAddAddressModal()"
            class="bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Add Your First Address
        </button>
    </div>
    @endif
</div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add New Address</h3>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="addressForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="modal_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="modal_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                </div>

                <!-- Phone -->
                <div>
                    <label for="modal_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" id="modal_phone" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                </div>

                <!-- Address Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Address Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="home" class="peer sr-only" checked>
                            <div class="px-4 py-3 border-2 border-gray-300 rounded-lg peer-checked:border-brand-blue peer-checked:bg-brand-blue-50 flex items-center gap-2 justify-center">
                                <i class="fas fa-home"></i>
                                <span class="font-medium">Home</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="office" class="peer sr-only">
                            <div class="px-4 py-3 border-2 border-gray-300 rounded-lg peer-checked:border-brand-blue peer-checked:bg-brand-blue-50 flex items-center gap-2 justify-center">
                                <i class="fas fa-building"></i>
                                <span class="font-medium">Office</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="modal_address_line_1" class="block text-sm font-semibold text-gray-700 mb-2">
                        Address Line 1 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="address_line_1" id="modal_address_line_1" required
                        placeholder="House number, street name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="modal_address_line_2" class="block text-sm font-semibold text-gray-700 mb-2">
                        Address Line 2
                    </label>
                    <input type="text" name="address_line_2" id="modal_address_line_2"
                        placeholder="Apartment, suite, unit, etc. (optional)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                </div>

                <!-- City, State, Postal Code -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="modal_city" class="block text-sm font-semibold text-gray-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" id="modal_city" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="modal_state" class="block text-sm font-semibold text-gray-700 mb-2">
                            State/Province
                        </label>
                        <input type="text" name="state" id="modal_state"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="modal_postal_code" class="block text-sm font-semibold text-gray-700 mb-2">
                            Postal Code
                        </label>
                        <input type="text" name="postal_code" id="modal_postal_code"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                    </div>
                </div>

                <!-- Default Address Checkbox -->
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_default" id="modal_is_default" value="1"
                            class="w-5 h-5 text-brand-blue border-gray-300 rounded focus:ring-brand-blue">
                        <span class="text-sm font-medium text-gray-700">Set as default address</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeAddressModal()"
                    class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddAddressModal() {
        document.getElementById('addressModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Add New Address';
        document.getElementById('addressForm').action = '{{ route("customer.addresses.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('addressForm').reset();
        document.body.style.overflow = 'hidden';
    }

    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editAddress(id) {
        // Implementation depends on your backend data structure
        // You would typically fetch the address data via AJAX and populate the form
        alert('Edit functionality - to be implemented with AJAX');
    }

    // Close modal on outside click
    document.getElementById('addressModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddressModal();
        }
    });
</script>
@endpush
@endsection