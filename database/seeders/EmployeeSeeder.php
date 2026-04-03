<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::create([
            'name' => 'Employee 1'
        ]);

        Employee::create([
            'name' => 'Employee 2'
        ]);

        Employee::create([
            'name' => 'Employee 3'
        ]);
    }
}
