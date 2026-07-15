@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Settings</h1>
        <p class="text-xs text-slate-500">Manage your store settings and preferences</p>
    </div>
</div>

{{-- Settings Form --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <form action="{{ route('admin.settings.update') }}" method="POST" x-data="{ activeTab: 'general' }"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tabs Navigation --}}
        <div class="border-b border-slate-200 bg-white">
            <div class="flex overflow-x-auto">
                <button type="button" @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'"
                    class="px-5 py-3 text-xs font-semibold border-b-2 transition whitespace-nowrap">
                    <i data-lucide="cog" class="w-3.5 h-3.5 inline mr-1.5"></i>General
                </button>
                <button type="button" @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'"
                    class="px-5 py-3 text-xs font-semibold border-b-2 transition whitespace-nowrap">
                    <i data-lucide="phone" class="w-3.5 h-3.5 inline mr-1.5"></i>Contact
                </button>
                <button type="button" @click="activeTab = 'social'"
                    :class="activeTab === 'social' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-300'"
                    class="px-5 py-3 text-xs font-semibold border-b-2 transition whitespace-nowrap">
                    <i data-lucide="share-2" class="w-3.5 h-3.5 inline mr-1.5"></i>Social Media
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-5 md:p-6 text-xs">
            {{-- General --}}
            <div x-show="activeTab === 'general'" x-transition>
                <h2 class="text-sm font-bold text-slate-900 mb-4">General Settings</h2>
                <div class="space-y-4 max-w-2xl">
                    <x-input name="site_name" type="text"
                        value="{{ old('site_name', $all_settings['general']['site_name']['value'] ?? '') }}"
                        label="Site Name" placeholder="Enter site name" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

                    <x-input name="site_tagline" type="text"
                        value="{{ old('site_tagline', $all_settings['general']['site_tagline']['value'] ?? '') }}"
                        label="Site Tagline" placeholder="Enter site tagline" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />

                    <div class="grid md:grid-cols-2 gap-4">
                        <x-input name="currency" type="text"
                            value="{{ old('currency', $all_settings['general']['currency']['value'] ?? '') }}"
                            label="Currency" placeholder="BDT" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <x-input name="currency_symbol" type="text"
                            value="{{ old('currency_symbol', $all_settings['general']['currency_symbol']['value'] ?? '') }}"
                            label="Currency Symbol" placeholder="৳" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <x-input name="business_day_start" type="time"
                            value="{{ old('business_day_start', $all_settings['general']['business_day_start']['value'] ?? '') }}"
                            label="Business day start" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                        <x-input name="business_day_end" type="time"
                            value="{{ old('business_day_end', $all_settings['general']['business_day_end']['value'] ?? '') }}"
                            label="Business day end" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    </div>

                    <div>
                        <x-file-input name="site_logo" label="Site Logo" accept="image/*" />
                        <p class="mt-1 text-[10px] text-slate-400">Upload your logo image</p>
                        @if($all_settings['general']['site_logo']['value'] ?? false)
                            <img src="{{ storage_url($all_settings['general']['site_logo']['value']) }}" alt="Site Logo" class="mt-2 h-10">
                        @endif
                    </div>

                    <div>
                        <x-file-input name="site_favicon" label="Site Favicon" accept="image/*" />
                        <p class="mt-1 text-[10px] text-slate-400">Upload your favicon image</p>
                        @if($all_settings['general']['site_favicon']['value'] ?? false)
                            <img src="{{ storage_url($all_settings['general']['site_favicon']['value']) }}" alt="Site Favicon" class="mt-2 h-10">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div x-show="activeTab === 'contact'" x-transition x-cloak>
                <h2 class="text-sm font-bold text-slate-900 mb-4">Contact Information</h2>
                <div class="space-y-4 max-w-2xl">
                    <x-input type="email" name="contact_email" label="Contact Email" placeholder="info@example.com"
                        value="{{ old('contact_email', $all_settings['contact']['contact_email']['value'] ?? '') }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <x-input type="text" name="contact_phone" label="Contact Phone" placeholder="+880 1700-000000"
                        value="{{ old('contact_phone', $all_settings['contact']['contact_phone']['value'] ?? '') }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <x-input type="text" name="whatsapp_number" label="WhatsApp Number" placeholder="+8801700000000"
                        value="{{ old('whatsapp_number', $all_settings['contact']['whatsapp_number']['value'] ?? '') }}" class="text-xs h-9 bg-slate-50/50 focus:bg-white" />
                    <p class="text-[10px] text-slate-400 -mt-3">Without spaces or dashes</p>
                    <x-textarea name="contact_address" label="Contact Address" rows="3"
                        placeholder="Enter your business address" class="text-xs bg-slate-50/50 focus:bg-white">{{ old('contact_address', $all_settings['contact']['contact_address']['value'] ?? '') }}</x-textarea>
                    <x-textarea name="google_maps_embed" label="Google Maps Embed Code" rows="3"
                        placeholder="Paste Google Maps embed code" class="text-xs bg-slate-50/50 focus:bg-white">{{ old('google_maps_embed', $all_settings['contact']['google_maps_embed']['value'] ?? '') }}</x-textarea>
                    <p class="text-[10px] text-slate-400 -mt-3">Paste the entire iframe embed code from Google Maps</p>
                </div>
            </div>

            {{-- Social Media --}}
            <div x-show="activeTab === 'social'" x-transition x-cloak>
                <h2 class="text-sm font-bold text-slate-900 mb-4">Social Media Links</h2>
                <div class="space-y-4 max-w-2xl">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">
                            <i class="fab fa-facebook text-blue-600"></i> Facebook URL
                        </label>
                        <input type="url" name="facebook_url"
                            value="{{ old('facebook_url', $all_settings['social']['facebook_url']['value'] ?? '') }}"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition"
                            placeholder="https://facebook.com/yourpage">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">
                            <i class="fab fa-instagram text-pink-600"></i> Instagram URL
                        </label>
                        <input type="url" name="instagram_url"
                            value="{{ old('instagram_url', $all_settings['social']['instagram_url']['value'] ?? '') }}"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition"
                            placeholder="https://instagram.com/yourpage">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">
                            <i class="fab fa-youtube text-red-600"></i> YouTube URL
                        </label>
                        <input type="url" name="youtube_url"
                            value="{{ old('youtube_url', $all_settings['social']['youtube_url']['value'] ?? '') }}"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition"
                            placeholder="https://youtube.com/yourchannel">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">
                            <i class="fab fa-tiktok text-slate-800"></i> TikTok URL
                        </label>
                        <input type="url" name="tiktok_url"
                            value="{{ old('tiktok_url', $all_settings['social']['tiktok_url']['value'] ?? '') }}"
                            class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition"
                            placeholder="https://tiktok.com/@yourpage">
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-2 px-5 md:px-6 py-4 bg-slate-50/40 border-t border-slate-200">
            <a href="{{ route('admin.dashboard') }}"
                class="px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition inline-flex items-center">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg shadow-sm hover:bg-slate-900 transition">
                <i data-lucide="save" class="w-3.5 h-3.5"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@endsection
