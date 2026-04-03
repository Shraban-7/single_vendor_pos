<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Inside Dhaka',
                'code' => 'inside_dhaka',
                'shipping_cost' => 60.00,
                'free_shipping_threshold' => 2000.00,
                'estimated_days' => '1-2 business days',
                'is_active' => true,
            ],
            [
                'name' => 'Outside Dhaka',
                'code' => 'outside_dhaka',
                'shipping_cost' => 120.00,
                'free_shipping_threshold' => 3000.00,
                'estimated_days' => '3-5 business days',
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['code' => $zone['code']],
                $zone
            );
        }
    }
}
