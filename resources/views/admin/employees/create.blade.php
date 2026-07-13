@extends('admin.layouts.app')

@section('title', 'Create Employee')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Add Employee</h1>
        <p class="text-xs text-slate-500">Configure credentials to provision a new administrative staff identity.</p>
    </div>
    <div>
        <a href="{{ route('admin.employees.index') }}"
            class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Roster</span>
        </a>
    </div>
</div>

{{-- High Density Form Workspace Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <form action="{{ route('admin.employees.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">

                {{-- Employee Name Input --}}
                <div class="md:col-span-2">
                    <x-input
                        name="name"
                        type="text"
                        label="Employee Name *"
                        placeholder="Enter full name parameters"
                        required
                        class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                {{-- Phone Input --}}
                <div>
                    <x-input
                        name="phone"
                        type="text"
                        label="Phone Identifier Number *"
                        placeholder="01XXXXXXXXX"
                        required
                        class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                {{-- Email Input --}}
                <div>
                    <x-input
                        name="email"
                        type="email"
                        label="Email Workspace Address"
                        placeholder="employee@example.com"
                        class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

                {{-- Security Password Input --}}
                <div class="md:col-span-2">
                    <x-input
                        name="password"
                        type="password"
                        label="Access Password *"
                        placeholder="Assign initial complex secure string keys"
                        required
                        class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                </div>

            </div>

            {{-- Form Operations Control Pipeline Bar --}}
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.employees.index') }}"
                    class="px-3 h-9 inline-flex items-center justify-center text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                    Cancel
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 shadow-sm transition">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Create Employee</span>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
