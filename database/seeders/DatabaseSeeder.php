<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AdminUserSeeder::class,
            SupplierSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class
        ]);

        if (app()->environment('local')) {
            $this->call([
                ProductSeeder::class,
                EmployeeSeeder::class,
            ]);
        }
    }
}
