@extends('admin.layouts.app')

@section('title', 'Account Settings')

@section('content')

<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Account Settings</h1>
        <p class="text-xs text-slate-500">Manage your personal profile and password</p>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 flex items-center gap-2 px-4 py-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @method('PUT')

    {{-- Profile Details --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Profile Information</h2>
        </div>
        <div class="p-6 space-y-4 text-xs">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white text-xl font-bold shrink-0">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Avatar</label>
                    <input type="file" name="avatar" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition">
                    <p class="text-[11px] text-slate-400 mt-1">PNG, JPG up to 2MB.</p>
                </div>
            </div>

            <x-input name="name" type="text" label="Full Name"
                     value="{{ old('name', $user->name) }}"
                     class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

            <div class="grid md:grid-cols-2 gap-4">
                <x-input name="email" type="email" label="Email Address"
                         value="{{ old('email', $user->email) }}"
                         class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

                <x-input name="phone" type="text" label="Phone"
                         value="{{ old('phone', $user->phone) }}"
                         class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Gender</label>
                    <select name="gender"
                            class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                        <option value="">Prefer not to say</option>
                        @foreach($genders as $g)
                            <option value="{{ $g }}" {{ old('gender', $user->gender?->value) === $g ? 'selected' : '' }}>
                                {{ ucfirst($g) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-input name="date_of_birth" type="date" label="Date of Birth"
                         value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                         class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
            </div>
        </div>
    </div>

    {{-- Password --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Change Password</h2>
        </div>
        <div class="p-6 space-y-4 text-xs">
            <p class="text-[11px] text-slate-400">Leave blank to keep your current password.</p>

            <x-input name="current_password" type="password" label="Current Password"
                     class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

            <x-input name="password" type="password" label="New Password"
                     class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

            <x-input name="password_confirmation" type="password" label="Confirm New Password"
                     class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

            <button type="submit" class="w-full h-10 inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                <i data-lucide="save" class="w-4 h-4"></i> Save Changes
            </button>
        </div>
    </div>
</form>

@endsection
