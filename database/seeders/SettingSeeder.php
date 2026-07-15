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
            ['key' => 'site_name', 'value' => 'Lara Pos', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Your Dream Destination', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'file', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'BDT', 'type' => 'text', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '৳', 'type' => 'text', 'group' => 'general'],
            ['key' => 'business_day_start', 'value' => '10:00', 'type' => 'time', 'group' => 'general'],
            ['key' => 'business_day_end', 'value' => '00:00', 'type' => 'time', 'group' => 'general'],

            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@slashfashion.com.bd', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+880 1700-000000', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'whatsapp_number', 'value' => '+8801700000000', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'House #123, Road #45, Gulshan-2, Dhaka-1212, Bangladesh', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'google_maps_embed', 'value' => null, 'type' => 'text', 'group' => 'contact'],

            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/larapos', 'type' => 'text', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/larapos', 'type' => 'text', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => null, 'type' => 'text', 'group' => 'social'],
            ['key' => 'tiktok_url', 'value' => null, 'type' => 'text', 'group' => 'social'],

        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
