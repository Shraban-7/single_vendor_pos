@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Settings</h1>
    <p class="text-gray-500">Manage your store settings and preferences</p>
</div>

{{-- Settings Form --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <form action="{{ route('admin.settings.update') }}" method="POST" x-data="{ activeTab: 'general' }" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 bg-white">
            <div class="flex overflow-x-auto">
                <button type="button" @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-cog mr-2"></i>General
                </button>
                <button type="button" @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-phone mr-2"></i>Contact
                </button>
                <button type="button" @click="activeTab = 'social'" :class="activeTab === 'social' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-share-alt mr-2"></i>Social Media
                </button>
                <button type="button" @click="activeTab = 'shipping'" :class="activeTab === 'shipping' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-truck mr-2"></i>Shipping
                </button>
                <button type="button" @click="activeTab = 'payment'" :class="activeTab === 'payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-credit-card mr-2"></i>Payment
                </button>
                <button type="button" @click="activeTab = 'order'" :class="activeTab === 'order' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                </button>
                <button type="button" @click="activeTab = 'sms'" :class="activeTab === 'sms' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-sms mr-2"></i>SMS
                </button>
                <button type="button" @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>SEO
                </button>
                <button type="button" @click="activeTab = 'policy'" :class="activeTab === 'policy' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap">
                    <i class="fas fa-file-alt mr-2"></i>Policies
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-6 md:p-8">
            {{-- General Settings --}}
            <div x-show="activeTab === 'general'" x-transition>
                <h2 class="text-xl font-bold text-gray-900 mb-6">General Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <x-input name="site_name" type="text" value="{{ old('site_name', $all_settings['general']['site_name']['value'] ?? '') }}"
                            label="Site Name" placeholder="Enter site name" />

                    </div>

                    <div>
                        <x-input name="site_tagline" type="text" value="{{ old('site_tagline', $all_settings['general']['site_tagline']['value'] ?? '') }}"
                            label="Site Tagline" placeholder="Enter site tagline" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <x-input name="currency" type="text" value="{{ old('currency', $all_settings['general']['currency']['value'] ?? '') }}"
                                label="Currency" placeholder="BDT" />
                        </div>

                        <div>
                            <x-input name="currency_symbol" type="text" value="{{ old('currency_symbol', $all_settings['general']['currency_symbol']['value'] ?? '') }}"
                                label="Currency Symbol" placeholder="৳" />
                        </div>
                    </div>

                    <div>
                        <x-file-input name="site_logo" label="Site Logo" accept="image/*" />
                        <p class="text-xs text-gray-500 mt-1">Upload your logo image</p>
                        @if($all_settings['general']['site_logo']['value'] ?? false)
                        <img src="{{ storage_url($all_settings['general']['site_logo']['value']) }}" alt="Site Logo" class="mt-2 h-12">
                        @endif
                    </div>

                    <div>
                        <x-file-input name="site_favicon" label="Site Favicon" accept="image/*" />
                        <p class="text-xs text-gray-500 mt-1">Upload your favicon image</p>
                        @if($all_settings['general']['site_favicon']['value'] ?? false)
                        <img src="{{ storage_url($all_settings['general']['site_favicon']['value']) }}" alt="Site Favicon" class="mt-2 h-12">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contact Settings --}}
            <div x-show="activeTab === 'contact'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <x-input
                            type="email"
                            name="contact_email"
                            label="Contact Email"
                            placeholder="info@example.com"
                            value="{{ old('contact_email', $all_settings['contact']['contact_email']['value'] ?? '') }}" />
                    </div>

                    <div>
                        <x-input
                            type="text"
                            name="contact_phone"
                            label="Contact Phone"
                            placeholder="+880 1700-000000"
                            value="{{ old('contact_phone', $all_settings['contact']['contact_phone']['value'] ?? '') }}" />
                    </div>

                    <div>
                        <x-input
                            type="text"
                            name="whatsapp_number"
                            label="WhatsApp Number"
                            placeholder="+8801700000000"
                            value="{{ old('whatsapp_number', $all_settings['contact']['whatsapp_number']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Without spaces or dashes</p>
                    </div>

                    <div>
                        <x-textarea
                            name="contact_address"
                            label="Contact Address"
                            rows="3"
                            placeholder="Enter your business address">{{ old('contact_address', $all_settings['contact']['contact_address']['value'] ?? '') }}
                        </x-textarea>
                    </div>

                    <div>
                        <x-textarea
                            name="google_maps_embed"
                            label="Google Maps Embed Code"
                            rows="3"
                            placeholder="Paste Google Maps embed code">{{ old('google_maps_embed', $all_settings['contact']['google_maps_embed']['value'] ?? '') }}
                        </x-textarea>
                        <p class="text-xs text-gray-500 mt-1">Paste the entire iframe embed code from Google Maps</p>
                    </div>
                </div>
            </div>

            {{-- Social Media Settings --}}
            <div x-show="activeTab === 'social'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Social Media Links</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-facebook text-blue-600"></i> Facebook URL
                        </label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $all_settings['social']['facebook_url']['value'] ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="https://facebook.com/yourpage">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-instagram text-pink-600"></i> Instagram URL
                        </label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $all_settings['social']['instagram_url']['value'] ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="https://instagram.com/yourpage">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-youtube text-red-600"></i> YouTube URL
                        </label>
                        <input type="url" name="youtube_url" value="{{ old('youtube_url', $all_settings['social']['youtube_url']['value'] ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="https://youtube.com/yourchannel">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-tiktok text-gray-800"></i> TikTok URL
                        </label>
                        <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $all_settings['social']['tiktok_url']['value'] ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="https://tiktok.com/@yourpage">
                    </div>
                </div>
            </div>

            {{-- Shipping Settings --}}
            <div x-show="activeTab === 'shipping'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Shipping Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div class="grid md:grid-cols-2 gap-6">
                        <x-input
                            type="number"
                            name="shipping_inside_dhaka"
                            label="Shipping Inside Dhaka (৳)"
                            placeholder="60"
                            value="{{ old('shipping_inside_dhaka', $all_settings['shipping']['shipping_inside_dhaka']['value'] ?? '') }}" />
                        <x-input
                            type="number"
                            name="shipping_outside_dhaka"
                            label="Shipping Outside Dhaka (৳)"
                            placeholder="120"
                            value="{{ old('shipping_outside_dhaka', $all_settings['shipping']['shipping_outside_dhaka']['value'] ?? '') }}" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <x-input
                                type="number"
                                name="free_shipping_threshold_dhaka"
                                label="Free Shipping Threshold - Dhaka (৳)"
                                placeholder="2000"
                                value="{{ old('free_shipping_threshold_dhaka', $all_settings['shipping']['free_shipping_threshold_dhaka']['value'] ?? '') }}" />
                            <p class="text-xs text-gray-500 mt-1">Minimum order for free shipping</p>
                        </div>

                        <div>
                            <x-input
                                type="number"
                                name="free_shipping_threshold_outside"
                                label="Free Shipping Threshold - Outside (৳)"
                                placeholder="3000"
                                value="{{ old('free_shipping_threshold_outside', $all_settings['shipping']['free_shipping_threshold_outside']['value'] ?? '') }}" />
                            <p class="text-xs text-gray-500 mt-1">Minimum order for free shipping</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">

                        <x-input
                            type="text"
                            name="delivery_time_dhaka"
                            label="Delivery Time - Dhaka"
                            placeholder="1-2 business days"
                            value="{{ old('delivery_time_dhaka', $all_settings['shipping']['delivery_time_dhaka']['value'] ?? '') }}" />

                        <x-input
                            type="text"
                            name="delivery_time_outside"
                            label="Delivery Time - Outside Dhaka"
                            placeholder="3-5 business days"
                            value="{{ old('delivery_time_outside', $all_settings['shipping']['delivery_time_outside']['value'] ?? '') }}" />
                    </div>
                </div>
            </div>

            {{-- Payment Settings --}}
            <div x-show="activeTab === 'payment'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Payment Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="cod_enabled" value="true" {{ old('cod_enabled', $all_settings['payment']['cod_enabled']['value'] ?? '') == 'true' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900">Enable Cash on Delivery (COD)</span>
                                <p class="text-xs text-gray-500 mt-1">Allow customers to pay with cash on delivery</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Order Settings --}}
            <div x-show="activeTab === 'order'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Order Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <x-input
                            type="text"
                            name="order_prefix"
                            label="Order Prefix"
                            placeholder="SF"
                            value="{{ old('order_prefix', $all_settings['order']['order_prefix']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Orders will be numbered as: PREFIX-001, PREFIX-002, etc.</p>
                    </div>

                    <div>
                        <x-input
                            type="number"
                            name="min_order_amount"
                            label="Minimum Order Amount (৳)"
                            placeholder="500"
                            value="{{ old('min_order_amount', $all_settings['order']['min_order_amount']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Minimum amount required to place an order</p>
                    </div>

                    <div>
                        <x-input
                            type="number"
                            name="max_order_quantity"
                            label="Maximum Order Quantity"
                            placeholder="10"
                            value="{{ old('max_order_quantity', $all_settings['order']['max_order_quantity']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Maximum quantity per product in a single order</p>
                    </div>

                    <div>
                        <x-input
                            type="number"
                            name="order_cancellation_hours"
                            label="Order Cancellation Window (Hours)"
                            placeholder="24"
                            value="{{ old('order_cancellation_hours', $all_settings['order']['order_cancellation_hours']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Hours within which customers can cancel orders</p>
                    </div>
                </div>
            </div>

            {{-- SMS Settings --}}
            <div x-show="activeTab === 'sms'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">SMS Notification Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="sms_enabled" value="true" {{ old('sms_enabled', $all_settings['sms']['sms_enabled']['value'] ?? '') == 'true' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900">Enable SMS Notifications</span>
                                <p class="text-xs text-gray-500 mt-1">Send SMS notifications to customers</p>
                            </div>
                        </label>
                    </div>

                    <div>
                        <x-input
                            type="text"
                            name="sms_provider"
                            label="SMS Provider"
                            placeholder="ssl_wireless"
                            value="{{ old('sms_provider', $all_settings['sms']['sms_provider']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">e.g., ssl_wireless, twilio, etc.</p>
                    </div>

                    <div>
                        <x-input
                            type="text"
                            name="sms_api_key"
                            label="SMS API Key"
                            placeholder="Enter API Key"
                            value="{{ old('sms_api_key', $all_settings['sms']['sms_api_key']['value'] ?? '') }}" />
                    </div>

                    <div>
                        <x-input
                            type="text"
                            name="sms_sender_id"
                            label="SMS Sender ID"
                            placeholder="Spinner Fashion"
                            value="{{ old('sms_sender_id', $all_settings['sms']['sms_sender_id']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Name that appears as sender in SMS</p>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            <div x-show="activeTab === 'seo'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">SEO & Analytics Settings</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <x-input
                            name="meta_title"
                            label="Meta Title"
                            placeholder="Your site title for SEO"
                            value="{{ old('meta_title', $all_settings['seo']['meta_title']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Appears in search engine results (50-60 characters)</p>
                    </div>

                    <div>
                        <x-textarea
                            name="meta_description"
                            label="Meta Description"
                            rows="3"
                            placeholder="Brief description of your site">{{ old('meta_description', $all_settings['seo']['meta_description']['value'] ?? '') }}
                        </x-textarea>
                        <p class="text-xs text-gray-500 mt-1">Brief description for search engines (150-160 characters)</p>
                    </div>

                    <div>
                        <x-input
                            name="meta_keywords"
                            label="Meta Keywords"
                            placeholder="keyword1, keyword2, keyword3"
                            value="{{ old('meta_keywords', $all_settings['seo']['meta_keywords']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                    </div>

                    <div>
                        <x-input
                            name="google_analytics_id"
                            label="Google Analytics ID"
                            placeholder="G-XXXXXXXXXX"
                            value="{{ old('google_analytics_id', $all_settings['seo']['google_analytics_id']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Google Analytics tracking ID</p>
                    </div>

                    <div>
                        <x-input
                            name="facebook_pixel_id"
                            label="Facebook Pixel ID"
                            placeholder="123456789012345"
                            value="{{ old('facebook_pixel_id', $all_settings['seo']['facebook_pixel_id']['value'] ?? '') }}" />
                        <p class="text-xs text-gray-500 mt-1">Facebook Pixel ID for tracking</p>
                    </div>
                </div>
            </div>

            {{-- Policy Settings --}}
            <div x-show="activeTab === 'policy'" x-transition x-cloak>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Store Policies</h2>
                <div class="space-y-6 max-w-2xl">
                    <div>
                        <x-textarea
                            name="return_policy"
                            label="Return Policy"
                            rows="4"
                            placeholder="Enter your return policy">{{ old('return_policy', $all_settings['policy']['return_policy']['value'] ?? '') }}</x-textarea>
                    </div>

                    <div>
                        <x-textarea
                            name="exchange_policy"
                            label="Exchange Policy"
                            rows="4"
                            placeholder="Enter your exchange policy">{{ old('exchange_policy', $all_settings['policy']['exchange_policy']['value'] ?? '') }}</x-textarea>
                    </div>

                    <div>
                        <x-textarea
                            name="refund_policy"
                            label="Refund Policy"
                            rows="4"
                            placeholder="Enter your refund policy">{{ old('refund_policy', $all_settings['policy']['refund_policy']['value'] ?? '') }}</x-textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-4 px-6 md:px-8 py-6 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl hover:from-blue-600 hover:to-blue-700 shadow-sm hover:shadow-md transition">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush