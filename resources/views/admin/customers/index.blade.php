@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
        <p class="text-sm text-gray-600">Manage your registered customer base</p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                        src="{{ $customer->image ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name) }}"
                                        alt="{{ $customer->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-400">ID: #{{ $customer->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900 font-medium">{{ $customer->phone }}</div>
                            <div class="text-xs text-gray-500">{{ $customer->email ?? 'No email' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex justify-end items-center gap-1">
                                <button onclick="toggleModal('editCustomer{{ $customer->id }}')"
                                    class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-md transition-all"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-base"></i>
                                </button>

                                <form action="" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-md transition-all"
                                        title="Delete">
                                        <i class="fa-solid fa-trash-can text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

@foreach($customers as $customer)
<div id="editCustomer{{ $customer->id }}" class="fixed inset-0 z-[60] flex items-center justify-center modal-overlay hidden-modal">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('editCustomer{{ $customer->id }}')"></div>

    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden modal-container">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Edit Customer Profile</h3>
            <button onclick="toggleModal('editCustomer{{ $customer->id }}')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 text-left">Full Name</label>
                    <input type="text" name="name" value="{{ $customer->name }}" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 text-left">Phone Number</label>
                    <input type="text" name="phone" value="{{ $customer->phone }}" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 text-left">New Password</label>
                    <input type="password" name="password" placeholder="Leave empty to keep current"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3">
                <button type="button" onclick="toggleModal('editCustomer{{ $customer->id }}')"
                    class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 shadow-md transition-all">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection