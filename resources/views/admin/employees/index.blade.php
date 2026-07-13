@extends('admin.layouts.app')

@section('title', 'Employees')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Employees</h1>
        <p class="text-xs text-slate-500">Manage, update internal permissions, and audit active staff metrics records.</p>
    </div>
    <div>
        <a href="{{ route('admin.employees.create') }}"
            class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Employee</span>
        </a>
    </div>
</div>

{{-- Compact Filter Framework --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.employees.index') }}" class="space-y-2.5">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

            {{-- Search Bar Component --}}
            <div class="sm:col-span-10">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search employee roster registry by full name or phone parameters..."
                        class="w-full pl-8 pr-3 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white transition">
                    <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                </div>
            </div>

            {{-- Filter Action Pipeline --}}
            <div class="sm:col-span-2 flex gap-1">
                <button type="submit" class="flex flex-1 items-center justify-center h-9 text-xs text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm" title="Apply Filters">
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Filter</span>
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.employees.index') }}" class="flex items-center justify-center w-9 h-9 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition shadow-sm" title="Reset Filters">
                        <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>

        </div>
    </form>
</div>

{{-- High Density Roster Data Matrix Table --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">Employee Identity</th>
                    <th class="px-4 py-3">Phone Identifier</th>
                    <th class="px-4 py-3">Email Workspace Address</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($employees as $employee)
                    <tr class="hover:bg-slate-50/60 transition-colors">

                        {{-- Identity Column --}}
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200/60 flex items-center justify-center text-slate-500 flex-shrink-0">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 text-sm block truncate max-w-[180px]">{{ $employee->name }}</span>
                                    <span class="px-1 py-0.1 text-[8px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100/60 rounded uppercase tracking-wide w-fit mt-0.5 block">Staff</span>
                                </div>
                            </div>
                        </td>

                        {{-- Phone Column --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                            {{ $employee->phone }}
                        </td>

                        {{-- Email Column --}}
                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                            {{ $employee->email ?? '—' }}
                        </td>

                        {{-- Row Action Tools --}}
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-0.5">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                    class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded transition"
                                    title="Edit Directory Information">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>

                                <button type="button"
                                    onclick="openDeleteModal('{{ route('admin.employees.destroy', $employee->id) }}', '{{ addslashes($employee->name) }}')"
                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded transition"
                                    title="Terminate Record">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-14 text-center text-slate-500">
                            <div class="max-w-xs mx-auto flex flex-col items-center">
                                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 mb-3 border border-slate-100">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </div>
                                <h3 class="font-bold text-slate-900">No employees found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">No employee records match the current grid query criteria parameters.</p>
                                @if(request()->filled('search'))
                                    <a href="{{ route('admin.employees.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-600 hover:underline">Clear Roster Filter</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Compact Pagination Footer Bar --}}
    @if($employees->hasPages())
        <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100 text-xs text-slate-600">
            {{ $employees->links() }}
        </div>
    @endif
</div>

{{-- Modernized Low-Profile Delete Confirmation Modal Element --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    {{-- Mask Layer Overlay --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeDeleteModal()"></div>

    {{-- Content Window Panel Container --}}
    <div class="relative bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-sm mx-auto overflow-hidden transition-all transform z-10 p-5">
        <div class="text-center mb-5">
            <div class="w-12 h-12 bg-rose-50 border border-rose-100 rounded-full flex items-center justify-center mx-auto mb-3 text-rose-600">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>

            <h3 class="text-base font-bold text-slate-900 mb-1">
                Delete Employee Profile?
            </h3>

            <p class="text-xs text-slate-500 leading-relaxed">
                Are you sure you want to completely drop <span id="deleteEmployeeName" class="font-bold text-slate-800"></span> from database registries? This operational pipeline execution cannot be reversed.
            </p>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button type="button" onclick="closeDeleteModal()"
                class="px-3 h-9 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                Cancel
            </button>

            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center justify-center px-3.5 h-9 text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 shadow-sm transition">
                    Confirm Deletion
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function openDeleteModal(action, employeeName) {
            document.getElementById('deleteForm').action = action;
            document.getElementById('deleteEmployeeName').innerText = employeeName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endpush
@endsection
