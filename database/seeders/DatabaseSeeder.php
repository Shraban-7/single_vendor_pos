<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShippingZoneSeeder::class,
            DistrictSeeder::class,
            SizeSeeder::class,
            ColorSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,

            AdminUserSeeder::class,

            CouponSeeder::class,
            BannerSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call([
                ProductSeeder::class,
                Employee::class,
            ]);
        }
    }
}
