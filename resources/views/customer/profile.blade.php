@extends('customer.layout')
@section('title', 'Profile')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-brand-blue">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-user"></i>
                Profile Information
            </h2>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $user->name) }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition @error('name') border-red-500 @enderror"
                            required>
                    </div>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $user->email) }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition @error('email') border-red-500 @enderror"
                            required>
                    </div>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="text"
                            name="phone"
                            id="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition @error('phone') border-red-500 @enderror"
                            placeholder="01712345678">
                    </div>
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end pt-4">
                    <button type="submit"
                        class="bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-700">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-lock"></i>
                Change Password
            </h2>
        </div>

        <form action="{{ route('customer.password.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input type="password"
                            name="current_password"
                            id="current_password"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition @error('current_password') border-red-500 @enderror"
                            required>
                    </div>
                    @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password"
                            name="new_password"
                            id="new_password"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition @error('new_password') border-red-500 @enderror"
                            required
                            minlength="8">
                    </div>
                    @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long.</p>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password"
                            name="new_password_confirmation"
                            id="new_password_confirmation"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition"
                            required
                            minlength="8">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end pt-4">
                    <button type="submit"
                        class="bg-gray-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i>
                        Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Account Details -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Account Details
            </h2>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                    <span class="text-gray-700 font-medium">Member Since</span>
                </div>
                <span class="text-gray-900 font-semibold">{{ $user->created_at->format('F d, Y') }}</span>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-clock text-gray-400"></i>
                    <span class="text-gray-700 font-medium">Last Updated</span>
                </div>
                <span class="text-gray-900 font-semibold">{{ $user->updated_at->format('F d, Y') }}</span>
            </div>

            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    <i class="fas fa-shield-alt text-gray-400"></i>
                    <span class="text-gray-700 font-medium">Account Status</span>
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                    <i class="fas fa-check-circle"></i>
                    Active
                </span>
            </div>
        </div>
    </div>
</div>
@endsection