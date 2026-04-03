<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'admin@spinnerfashion.com.bd'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@spinnerfashion.com.bd',
                'phone' => '01700000000',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'phone_verified_at' => now(),
            ]
        );

        // Create Manager
        User::updateOrCreate(
            ['email' => 'manager@spinnerfashion.com.bd'],
            [
                'name' => 'Store Manager',
                'email' => 'manager@spinnerfashion.com.bd',
                'phone' => '01700000001',
                'password' => Hash::make('password'),
                'role' => UserRole::MANAGER,
                'is_active' => true,
                'phone_verified_at' => now(),
            ]
        );

        // Create Staff
        User::updateOrCreate(
            ['email' => 'staff@spinnerfashion.com.bd'],
            [
                'name' => 'Staff Member',
                'email' => 'staff@spinnerfashion.com.bd',
                'phone' => '01700000002',
                'password' => Hash::make('password'),
                'role' => UserRole::STAFF,
                'is_active' => true,
                'phone_verified_at' => now(),
            ]
        );
    }
}
