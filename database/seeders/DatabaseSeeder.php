<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DistrictSeeder::class,
            SettingSeeder::class,
            AdminUserSeeder::class,
            CouponSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call([
                ProductSeeder::class,
                EmployeeSeeder::class,
            ]);
        }
    }
}
