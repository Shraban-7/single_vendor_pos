<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Spinner Fashion', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Your Fashion Destination', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'BDT', 'type' => 'text', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '৳', 'type' => 'text', 'group' => 'general'],

            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@spinnerfashion.com.bd', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+880 1700-000000', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'whatsapp_number', 'value' => '+8801700000000', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'House #123, Road #45, Gulshan-2, Dhaka-1212, Bangladesh', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'google_maps_embed', 'value' => null, 'type' => 'text', 'group' => 'contact'],

            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/spinnerfashionbd', 'type' => 'text', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/spinnerfashionbd', 'type' => 'text', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => null, 'type' => 'text', 'group' => 'social'],
            ['key' => 'tiktok_url', 'value' => null, 'type' => 'text', 'group' => 'social'],

            // Shipping Settings
            ['key' => 'shipping_inside_dhaka', 'value' => '60', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'shipping_outside_dhaka', 'value' => '120', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'free_shipping_threshold_dhaka', 'value' => '2000', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'free_shipping_threshold_outside', 'value' => '3000', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'delivery_time_dhaka', 'value' => '1-2 business days', 'type' => 'text', 'group' => 'shipping'],
            ['key' => 'delivery_time_outside', 'value' => '3-5 business days', 'type' => 'text', 'group' => 'shipping'],

            // Payment Settings
            ['key' => 'cod_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment'],

            // SMS Settings
            ['key' => 'sms_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'sms'],
            ['key' => 'sms_provider', 'value' => 'ssl_wireless', 'type' => 'text', 'group' => 'sms'],
            ['key' => 'sms_api_key', 'value' => null, 'type' => 'text', 'group' => 'sms'],
            ['key' => 'sms_sender_id', 'value' => 'Spinner Fashion', 'type' => 'text', 'group' => 'sms'],

            // Order Settings
            ['key' => 'order_prefix', 'value' => 'SF', 'type' => 'text', 'group' => 'order'],
            ['key' => 'min_order_amount', 'value' => '500', 'type' => 'number', 'group' => 'order'],
            ['key' => 'max_order_quantity', 'value' => '10', 'type' => 'number', 'group' => 'order'],
            ['key' => 'order_cancellation_hours', 'value' => '24', 'type' => 'number', 'group' => 'order'],

            // SEO Settings
            ['key' => 'meta_title', 'value' => 'Spinner Fashion - Best Online Fashion Store in Bangladesh', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Shop latest fashion trends for men, women & kids. Best quality clothing at affordable prices with home delivery across Bangladesh.', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'meta_keywords', 'value' => 'fashion, clothing, online shopping, bangladesh, panjabi, saree, t-shirt', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => null, 'type' => 'text', 'group' => 'seo'],
            ['key' => 'facebook_pixel_id', 'value' => null, 'type' => 'text', 'group' => 'seo'],

            // Store Policies
            ['key' => 'return_policy', 'value' => 'Returns accepted within 7 days of delivery. Product must be unused and in original packaging.', 'type' => 'text', 'group' => 'policy'],
            ['key' => 'exchange_policy', 'value' => 'Exchange available within 7 days. Size exchange is free for first time.', 'type' => 'text', 'group' => 'policy'],
            ['key' => 'refund_policy', 'value' => 'Refunds are processed within 5-7 business days after return approval.', 'type' => 'text', 'group' => 'policy'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
