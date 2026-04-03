<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'XS', 'code' => 'xs', 'sort_order' => 1],
            ['name' => 'S', 'code' => 's', 'sort_order' => 2],
            ['name' => 'M', 'code' => 'm', 'sort_order' => 3],
            ['name' => 'L', 'code' => 'l', 'sort_order' => 4],
            ['name' => 'XL', 'code' => 'xl', 'sort_order' => 5],
            ['name' => 'XXL', 'code' => 'xxl', 'sort_order' => 6],
            ['name' => '3XL', 'code' => '3xl', 'sort_order' => 7],
            ['name' => '4XL', 'code' => '4xl', 'sort_order' => 8],
            // Numeric sizes for pants/jeans
            ['name' => '28', 'code' => '28', 'sort_order' => 10],
            ['name' => '30', 'code' => '30', 'sort_order' => 11],
            ['name' => '32', 'code' => '32', 'sort_order' => 12],
            ['name' => '34', 'code' => '34', 'sort_order' => 13],
            ['name' => '36', 'code' => '36', 'sort_order' => 14],
            ['name' => '38', 'code' => '38', 'sort_order' => 15],
            // Shoe sizes
            ['name' => '39', 'code' => '39', 'sort_order' => 30],
            ['name' => '40 (Shoe)', 'code' => '40-shoe', 'sort_order' => 31],
            ['name' => '41', 'code' => '41', 'sort_order' => 32],
            ['name' => '42 (Shoe)', 'code' => '42-shoe', 'sort_order' => 33],
            ['name' => '43', 'code' => '43', 'sort_order' => 34],
            ['name' => '44', 'code' => '44', 'sort_order' => 35],
            // Kids sizes
            ['name' => '2 Years', 'code' => '2y', 'sort_order' => 40],
            ['name' => '4 Years', 'code' => '4y', 'sort_order' => 41],
            ['name' => '6 Years', 'code' => '6y', 'sort_order' => 42],
            ['name' => '8 Years', 'code' => '8y', 'sort_order' => 43],
            ['name' => '10 Years', 'code' => '10y', 'sort_order' => 44],
            ['name' => '12 Years', 'code' => '12y', 'sort_order' => 45],
            // Baby sizes
            ['name' => '0-6 Months', 'code' => '0-6m', 'sort_order' => 50],
            ['name' => '6-12 Months', 'code' => '6-12m', 'sort_order' => 51],
            ['name' => '12-18 Months', 'code' => '12-18m', 'sort_order' => 52],
            ['name' => '18-24 Months', 'code' => '18-24m', 'sort_order' => 53],
            // Free size
            ['name' => 'Free Size', 'code' => 'free', 'sort_order' => 60],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(
                ['code' => $size['code']],
                $size
            );
        }
    }
}
