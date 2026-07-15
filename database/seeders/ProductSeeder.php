<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Product Units
        $units = [
            ['name' => 'Piece', 'name_bn' => 'পিস', 'short_name' => 'pc', 'short_name_bn' => 'পিস', 'type' => 'quantity'],
            ['name' => 'Kilogram', 'name_bn' => 'কেজি', 'short_name' => 'kg', 'short_name_bn' => 'কেজি', 'type' => 'weight'],
            ['name' => 'Liter', 'name_bn' => 'লিটার', 'short_name' => 'L', 'short_name_bn' => 'লিটার', 'type' => 'volume'],
            ['name' => 'Box', 'name_bn' => 'বক্স', 'short_name' => 'box', 'short_name_bn' => 'বক্স', 'type' => 'quantity'],
            ['name' => 'Packet', 'name_bn' => 'প্যাকেট', 'short_name' => 'pkt', 'short_name_bn' => 'প্যাকেট', 'type' => 'quantity'],
        ];

        $unitIds = [];
        foreach ($units as $unit) {
            $createdUnit = Unit::updateOrCreate(
                ['name' => $unit['name']],
                $unit
            );
            $unitIds[] = $createdUnit->id;
        }

        // 2. Seed Product Categories (global - no user_id, no duplicates)
        $categoriesData = [['name' => 'Food Items', 'name_bn' => 'Food Items', 'icon' => 'shopping-basket', 'color' => '#FF5733'], ['name' => 'Electronics', 'name_bn' => 'Electronics', 'icon' => 'plug', 'color' => '#33FF57'], ['name' => 'Cosmetics & Beauty', 'name_bn' => 'Cosmetics & Beauty', 'icon' => 'sparkles', 'color' => '#3357FF'], ['name' => 'Clothing & Fashion', 'name_bn' => 'Clothing & Fashion', 'icon' => 'shirt', 'color' => '#F333FF'], ['name' => 'Beverages', 'name_bn' => 'Beverages', 'icon' => 'cup-soda', 'color' => '#17A2B8'], ['name' => 'Footwear', 'name_bn' => 'Footwear', 'icon' => 'shoe', 'color' => '#795548'], ['name' => 'Mobile & Accessories', 'name_bn' => 'Mobile & Accessories', 'icon' => 'smartphone', 'color' => '#2196F3'], ['name' => 'Computer & IT', 'name_bn' => 'Computer & IT', 'icon' => 'laptop', 'color' => '#3F51B5'], ['name' => 'Home Appliances', 'name_bn' => 'Home Appliances', 'icon' => 'tv', 'color' => '#009688'], ['name' => 'Medicine & Pharmacy', 'name_bn' => 'Medicine & Pharmacy', 'icon' => 'pill', 'color' => '#4CAF50'], ['name' => 'Health & Wellness', 'name_bn' => 'Health & Wellness', 'icon' => 'heart', 'color' => '#F44336'], ['name' => 'Baby & Kids Products', 'name_bn' => 'Baby & Kids Products', 'icon' => 'baby', 'color' => '#E91E63'], ['name' => 'Stationery & Books', 'name_bn' => 'Stationery & Books', 'icon' => 'book', 'color' => '#673AB7'], ['name' => 'Sports & Fitness', 'name_bn' => 'Sports & Fitness', 'icon' => 'dumbbell', 'color' => '#FF5722'], ['name' => 'Toys & Games', 'name_bn' => 'Toys & Games', 'icon' => 'puzzle', 'color' => '#9C27B0'], ['name' => 'Furniture', 'name_bn' => 'Furniture', 'icon' => 'couch', 'color' => '#8D6E63'], ['name' => 'Home & Kitchen', 'name_bn' => 'Home & Kitchen', 'icon' => 'utensils', 'color' => '#FF5722'], ['name' => 'Hardware & Tools', 'name_bn' => 'Hardware & Tools', 'icon' => 'wrench', 'color' => '#607D8B'], ['name' => 'Electrical & Plumbing', 'name_bn' => 'Electrical & Plumbing', 'icon' => 'zap', 'color' => '#FFC107'], ['name' => 'Auto Parts & Accessories', 'name_bn' => 'Auto Parts & Accessories', 'icon' => 'car', 'color' => '#333333'], ['name' => 'Agricultural Supplies', 'name_bn' => 'Agricultural Supplies', 'icon' => 'tractor', 'color' => '#4CAF50'], ['name' => 'Pet Supplies', 'name_bn' => 'Pet Supplies', 'icon' => 'paw-print', 'color' => '#795548'], ['name' => 'Jewelry & Accessories', 'name_bn' => 'Jewelry & Accessories', 'icon' => 'gem', 'color' => '#FFD700'], ['name' => 'Bags & Luggage', 'name_bn' => 'Bags & Luggage', 'icon' => 'backpack', 'color' => '#9C27B0'], ['name' => 'Gifts & Flowers', 'name_bn' => 'Gifts & Flowers', 'icon' => 'gift', 'color' => '#E91E63'], ['name' => 'Optical & Eyewear', 'name_bn' => 'Optical & Eyewear', 'icon' => 'glasses', 'color' => '#607D8B'], ['name' => 'Watches', 'name_bn' => 'Watches', 'icon' => 'clock', 'color' => '#607D8B'], ['name' => 'General Store', 'name_bn' => 'General Store', 'icon' => 'store', 'color' => '#333333'],];

        $categoryIds = [];
        foreach ($categoriesData as $cat) {
            $createdCategory = Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'name_bn' => $cat['name_bn'],
                    'slug' => Str::slug($cat['name']),
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'is_active' => true,
                ]
            );
            $categoryIds[] = $createdCategory->id;
        }

        // 3. Seed products for each user (using global categories)
        $productsData = [['name' => 'Miniket Rice', 'category_index' => 0, 'cost' => 65.00, 'sell' => 72.00, 'stock' => 50.00, 'alert' => 10.00, 'unit_index' => 1], ['name' => 'Red Lentils', 'category_index' => 0, 'cost' => 110.00, 'sell' => 125.00, 'stock' => 30.00, 'alert' => 5.00, 'unit_index' => 1], ['name' => 'Soybean Oil 5 Liters', 'category_index' => 0, 'cost' => 810.00, 'sell' => 845.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 2], ['name' => 'Sugar', 'category_index' => 0, 'cost' => 130.00, 'sell' => 140.00, 'stock' => 40.00, 'alert' => 10.00, 'unit_index' => 1], ['name' => 'Local Onion', 'category_index' => 0, 'cost' => 75.00, 'sell' => 85.00, 'stock' => 100.00, 'alert' => 20.00, 'unit_index' => 1], ['name' => 'Diamond Potato', 'category_index' => 0, 'cost' => 32.00, 'sell' => 40.00, 'stock' => 120.00, 'alert' => 25.00, 'unit_index' => 1], ['name' => 'Red Flour 2 KG', 'category_index' => 0, 'cost' => 115.00, 'sell' => 130.00, 'stock' => 25.00, 'alert' => 5.00, 'unit_index' => 4], ['name' => 'Diploma Powder Milk 1 KG', 'category_index' => 0, 'cost' => 820.00, 'sell' => 860.00, 'stock' => 10.00, 'alert' => 2.00, 'unit_index' => 4], ['name' => 'Molla Salt', 'category_index' => 0, 'cost' => 38.00, 'sell' => 42.00, 'stock' => 60.00, 'alert' => 15.00, 'unit_index' => 4], ['name' => 'Radhuni Turmeric Powder', 'category_index' => 0, 'cost' => 45.00, 'sell' => 50.00, 'stock' => 50.00, 'alert' => 10.00, 'unit_index' => 4], ['name' => 'Philips LED Bulb 20 Watt', 'category_index' => 1, 'cost' => 280.00, 'sell' => 350.00, 'stock' => 20.00, 'alert' => 5.00, 'unit_index' => 0], ['name' => 'Defender Rechargeable Fan', 'category_index' => 1, 'cost' => 3800.00, 'sell' => 4500.00, 'stock' => 8.00, 'alert' => 2.00, 'unit_index' => 0], ['name' => 'Samsung Type-C Charger', 'category_index' => 1, 'cost' => 450.00, 'sell' => 600.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 0], ['name' => 'Click 5-Socket Power Strip', 'category_index' => 1, 'cost' => 320.00, 'sell' => 420.00, 'stock' => 12.00, 'alert' => 3.00, 'unit_index' => 0], ['name' => 'Parachute Coconut Oil 200 ML', 'category_index' => 2, 'cost' => 140.00, 'sell' => 165.00, 'stock' => 30.00, 'alert' => 6.00, 'unit_index' => 0], ['name' => 'Lux Bathing Soap', 'category_index' => 2, 'cost' => 68.00, 'sell' => 75.00, 'stock' => 80.00, 'alert' => 20.00, 'unit_index' => 0], ['name' => 'Sunsilk Shampoo Mini Pack', 'category_index' => 2, 'cost' => 180.00, 'sell' => 200.00, 'stock' => 100.00, 'alert' => 25.00, 'unit_index' => 4], ['name' => 'Colgate Toothpaste 150 Gram', 'category_index' => 2, 'cost' => 135.00, 'sell' => 150.00, 'stock' => 40.00, 'alert' => 10.00, 'unit_index' => 0], ['name' => 'ATM Cotton Lungi', 'category_index' => 3, 'cost' => 350.00, 'sell' => 450.00, 'stock' => 25.00, 'alert' => 5.00, 'unit_index' => 0], ['name' => 'Jaipuri Cotton Three-Piece', 'category_index' => 3, 'cost' => 850.00, 'sell' => 1150.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 0],];

        $users = User::where('role', 'admin')->get();

        foreach ($users as $user) {
            foreach ($productsData as $index => $prod) {
                $paddedCount = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $sku = 'PROD-' . $user->id . '-' . $paddedCount;
                $barcode = '880' . str_pad($user->id, 3, '0', STR_PAD_LEFT) . $paddedCount . rand(100, 999);

                Product::create([
                    'user_id' => $user->id,
                    'category_id' => $categoryIds[$prod['category_index']],
                    'unit_id' => $unitIds[$prod['unit_index']],
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'name' => $prod['name'],
                    'description' => $prod['name'] . ' - সিস্টেম কর্তৃক তৈরি ডেমো প্রোডাক্ট।',
                    'cost_price' => $prod['cost'],
                    'selling_price' => $prod['sell'],
                    'stock_in' => $prod['stock'],
                    'stock_out' => 0.00,
                    'stock_quantity' => $prod['stock'],
                    'stock_alert_quantity' => $prod['alert'],
                    'vat_rate' => 0.00,
                    'is_active' => true,
                    'is_returnable' => true,
                ]);
            }
        }
    }
}
