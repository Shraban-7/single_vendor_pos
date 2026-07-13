@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Customers</h1>
        <p class="text-sm text-slate-600">Manage your registered customer base</p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full object-cover border border-slate-200"
                                        src="{{ $customer->image ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name) }}"
                                        alt="{{ $customer->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-slate-400">ID: #{{ $customer->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-slate-900 font-medium">{{ $customer->phone }}</div>
                            <div class="text-xs text-slate-500">{{ $customer->email ?? 'No email' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex justify-end items-center gap-1">
                                <button onclick="toggleModal('editCustomer{{ $customer->id }}')"
                                    class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-md transition-all"
                                        title="Edit">
                                        <i data-lucide="square-pen"  class="w-4 h-4 fill-current text-base"></i>
                                </button>

                                <form action="" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-md transition-all"
                                            title="Delete">
                                            <i data-lucide="trash-2"  class="w-4 h-4 fill-current text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

@foreach($customers as $customer)
<div id="editCustomer{{ $customer->id }}" class="fixed inset-0 z-[60] flex items-center justify-center modal-overlay hidden-modal">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleModal('editCustomer{{ $customer->id }}')"></div>

    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden modal-container">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Edit Customer Profile</h3>
            <button onclick="toggleModal('editCustomer{{ $customer->id }}')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1 text-left">Full Name</label>
                    <input type="text" name="name" value="{{ $customer->name }}" required
                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1 text-left">Phone Number</label>
                    <input type="text" name="phone" value="{{ $customer->phone }}" required
                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1 text-left">New Password</label>
                    <input type="password" name="password" placeholder="Leave empty to keep current"
                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition p-2.5 border">
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-3">
                <button type="button" onclick="toggleModal('editCustomer{{ $customer->id }}')"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
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