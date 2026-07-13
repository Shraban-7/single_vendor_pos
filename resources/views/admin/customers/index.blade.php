@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Customers</h1>
        <p class="text-xs text-slate-500">Manage, verify, and monitor your active customer accounts database.</p>
    </div>
</div>

{{-- Compact Filter Framework --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="space-y-2.5">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

            {{-- Unified Search Component --}}
            <div class="sm:col-span-10">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search customer base by full name or active phone parameter..."
                        class="w-full pl-8 pr-3 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white transition">
                    <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                </div>
            </div>

            {{-- Action Pipelines --}}
            <div class="sm:col-span-2 flex gap-1">
                <button type="submit" class="flex flex-1 items-center justify-center h-9 text-xs text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm" title="Apply Parameters">
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Search</span>
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center justify-center w-9 h-9 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition shadow-sm" title="Reset Catalog Filter">
                        <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>

        </div>
    </form>
</div>

{{-- Data Dense Customers Table Card --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Customer Profile</th>
                    <th class="px-4 py-3">Contact Metrics</th>
                    <th class="px-4 py-3">Account Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        {{-- Customer Identity --}}
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <img class="h-8 w-8 rounded-full object-cover border border-slate-200 bg-slate-50 flex-shrink-0"
                                    src="{{ $customer->image ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name) }}"
                                    alt="{{ $customer->name }}">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm block truncate max-w-[180px]">{{ $customer->name }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-mono">ID: #{{ $customer->id }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Contact Parameters --}}
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-medium text-slate-700 block text-xs">{{ $customer->phone }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5 tracking-tight">{{ $customer->email ?? 'No email registered' }}</span>
                        </td>

                        {{-- Account Status Badge --}}
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @if($customer->is_active)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-700 border border-rose-100/70">Inactive</span>
                            @endif
                        </td>

                        {{-- Table Actions Pipeline --}}
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-0.5">
                                <button onclick="toggleModal('editCustomer{{ $customer->id }}')"
                                    class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded transition"
                                    title="Edit Profile">
                                    <i data-lucide="square-pen" class="w-3.5 h-3.5"></i>
                                </button>

                                <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer account? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded transition"
                                        title="Delete Account">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-14 text-center text-slate-500">
                            <div class="max-w-xs mx-auto flex flex-col items-center">
                                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 mb-3 border border-slate-100">
                                    <i data-lucide="users-2" class="w-5 h-5"></i>
                                </div>
                                <h3 class="font-bold text-slate-900">No matching records found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">We couldn't find any customers matching your combination of keyword filters.</p>
                                @if(request()->filled('search'))
                                    <a href="{{ route('admin.customers.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600 hover:underline">Reset Segmentation Grid</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Compact Footer Pagination --}}
    @if($customers->hasPages())
        <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100 text-xs text-slate-600">
            {{ $customers->links() }}
        </div>
    @endif
</div>

{{-- Modals Loop Segment --}}
@foreach($customers as $customer)
<div id="editCustomer{{ $customer->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="toggleModal('editCustomer{{ $customer->id }}')"></div>

    <div class="relative bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-sm mx-auto overflow-hidden transition-all transform z-10">
        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
            <h3 class="text-sm font-bold text-slate-900">Edit Customer Profile</h3>
            <button onclick="toggleModal('editCustomer{{ $customer->id }}')" class="text-slate-400 hover:text-slate-600 text-base transition focus:outline-none">&times;</button>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1 text-left">Full Name *</label>
                    <input type="text" name="name" value="{{ $customer->name }}" required
                        class="block w-full px-2.5 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1 text-left">Phone Number *</label>
                    <input type="text" name="phone" value="{{ $customer->phone }}" required
                        class="block w-full px-2.5 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1 text-left">New Security Password</label>
                    <input type="password" name="password" placeholder="Leave blank to maintain current identity parameters"
                        class="block w-full px-2.5 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition placeholder:text-[10px]">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="toggleModal('editCustomer{{ $customer->id }}')"
                    class="px-3 h-9 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 shadow-sm transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
