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
        $categoriesData = [
            ['name' => 'Food Items', 'name_bn' => 'খাদ্যপণ্য', 'icon' => 'shopping-basket', 'color' => '#FF5733'],
            ['name' => 'Electronics', 'name_bn' => 'ইলেকট্রনিক্স', 'icon' => 'plug', 'color' => '#33FF57'],
            ['name' => 'Cosmetics & Beauty', 'name_bn' => 'কসমেটিকস ও বিউটি', 'icon' => 'sparkles', 'color' => '#3357FF'],
            ['name' => 'Clothing & Fashion', 'name_bn' => 'পোশাক ও ফ্যাশন', 'icon' => 'shirt', 'color' => '#F333FF'],
            ['name' => 'Beverages', 'name_bn' => 'পানীয়', 'icon' => 'cup-soda', 'color' => '#17A2B8'],
            ['name' => 'Footwear', 'name_bn' => 'জুতা ও পাদুকা', 'icon' => 'shoe', 'color' => '#795548'],
            ['name' => 'Mobile & Accessories', 'name_bn' => 'মোবাইল ও আনুষাঙ্গিক', 'icon' => 'smartphone', 'color' => '#2196F3'],
            ['name' => 'Computer & IT', 'name_bn' => 'কম্পিউটার ও আইটি', 'icon' => 'laptop', 'color' => '#3F51B5'],
            ['name' => 'Home Appliances', 'name_bn' => 'গৃহস্থালী যন্ত্রপাতি', 'icon' => 'tv', 'color' => '#009688'],
            ['name' => 'Medicine & Pharmacy', 'name_bn' => 'ঔষধ ও ফার্মেসি', 'icon' => 'pill', 'color' => '#4CAF50'],
            ['name' => 'Health & Wellness', 'name_bn' => 'স্বাস্থ্য ও সুস্থতা', 'icon' => 'heart', 'color' => '#F44336'],
            ['name' => 'Baby & Kids Products', 'name_bn' => 'শিশুপণ্য', 'icon' => 'baby', 'color' => '#E91E63'],
            ['name' => 'Stationery & Books', 'name_bn' => 'স্টেশনারি ও বই', 'icon' => 'book', 'color' => '#673AB7'],
            ['name' => 'Sports & Fitness', 'name_bn' => 'খেলাধুলা ও ফিটনেস', 'icon' => 'dumbbell', 'color' => '#FF5722'],
            ['name' => 'Toys & Games', 'name_bn' => 'খেলনা ও গেম', 'icon' => 'puzzle', 'color' => '#9C27B0'],
            ['name' => 'Furniture', 'name_bn' => 'আসবাবপত্র', 'icon' => 'couch', 'color' => '#8D6E63'],
            ['name' => 'Home & Kitchen', 'name_bn' => 'গৃহস্থালী ও রান্নাঘর', 'icon' => 'utensils', 'color' => '#FF5722'],
            ['name' => 'Hardware & Tools', 'name_bn' => 'হার্ডওয়্যার ও সরঞ্জাম', 'icon' => 'wrench', 'color' => '#607D8B'],
            ['name' => 'Electrical & Plumbing', 'name_bn' => 'বৈদ্যুতিক ও প্লাম্বিং', 'icon' => 'zap', 'color' => '#FFC107'],
            ['name' => 'Auto Parts & Accessories', 'name_bn' => 'গাড়ির যন্ত্রাংশ ও আনুষাঙ্গিক', 'icon' => 'car', 'color' => '#333333'],
            ['name' => 'Agricultural Supplies', 'name_bn' => 'কৃষি সরঞ্জাম', 'icon' => 'tractor', 'color' => '#4CAF50'],
            ['name' => 'Pet Supplies', 'name_bn' => 'পোষাপ্রাণীর সরঞ্জাম', 'icon' => 'paw-print', 'color' => '#795548'],
            ['name' => 'Jewelry & Accessories', 'name_bn' => 'অলঙ্কার ও আনুষাঙ্গিক', 'icon' => 'gem', 'color' => '#FFD700'],
            ['name' => 'Bags & Luggage', 'name_bn' => 'ব্যাগ ও লাগেজ', 'icon' => 'backpack', 'color' => '#9C27B0'],
            ['name' => 'Gifts & Flowers', 'name_bn' => 'উপহার ও ফুল', 'icon' => 'gift', 'color' => '#E91E63'],
            ['name' => 'Optical & Eyewear', 'name_bn' => 'চশমা ও চক্ষু সামগ্রী', 'icon' => 'glasses', 'color' => '#607D8B'],
            ['name' => 'Watches', 'name_bn' => 'ঘড়ি', 'icon' => 'clock', 'color' => '#607D8B'],
            ['name' => 'General Store', 'name_bn' => 'জেনারেল স্টোর', 'icon' => 'store', 'color' => '#333333'],
        ];

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
        $productsData = [
            // Groceries
            ['name' => 'মিনিকেট চাল', 'category_index' => 0, 'cost' => 65.00, 'sell' => 72.00, 'stock' => 50.00, 'alert' => 10.00, 'unit_index' => 1],
            ['name' => 'মসুর ডাল', 'category_index' => 0, 'cost' => 110.00, 'sell' => 125.00, 'stock' => 30.00, 'alert' => 5.00, 'unit_index' => 1],
            ['name' => 'সয়াবিন তেল ৫ লিটার', 'category_index' => 0, 'cost' => 810.00, 'sell' => 845.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 2],
            ['name' => 'চিনি', 'category_index' => 0, 'cost' => 130.00, 'sell' => 140.00, 'stock' => 40.00, 'alert' => 10.00, 'unit_index' => 1],
            ['name' => 'দেশী পেঁয়াজ', 'category_index' => 0, 'cost' => 75.00, 'sell' => 85.00, 'stock' => 100.00, 'alert' => 20.00, 'unit_index' => 1],
            ['name' => 'ডায়মন্ড আলু', 'category_index' => 0, 'cost' => 32.00, 'sell' => 40.00, 'stock' => 120.00, 'alert' => 25.00, 'unit_index' => 1],
            ['name' => 'লাল আটা ২ কেজি', 'category_index' => 0, 'cost' => 115.00, 'sell' => 130.00, 'stock' => 25.00, 'alert' => 5.00, 'unit_index' => 4],
            ['name' => 'ডিপ্লোমা গুড়ো দুধ ১ কেজি', 'category_index' => 0, 'cost' => 820.00, 'sell' => 860.00, 'stock' => 10.00, 'alert' => 2.00, 'unit_index' => 4],
            ['name' => 'মোল্লা সল্ট লবন', 'category_index' => 0, 'cost' => 38.00, 'sell' => 42.00, 'stock' => 60.00, 'alert' => 15.00, 'unit_index' => 4],
            ['name' => 'রাধুনী হলুদের গুড়া', 'category_index' => 0, 'cost' => 45.00, 'sell' => 50.00, 'stock' => 50.00, 'alert' => 10.00, 'unit_index' => 4],
            // Electronics
            ['name' => 'ফিলিপস এলইডি বাল্ব ২০ ওয়াট', 'category_index' => 1, 'cost' => 280.00, 'sell' => 350.00, 'stock' => 20.00, 'alert' => 5.00, 'unit_index' => 0],
            ['name' => 'ডিফেন্ডার চার্জার ফ্যান', 'category_index' => 1, 'cost' => 3800.00, 'sell' => 4500.00, 'stock' => 8.00, 'alert' => 2.00, 'unit_index' => 0],
            ['name' => 'স্যামসাং টাইপ-সি চার্জার', 'category_index' => 1, 'cost' => 450.00, 'sell' => 600.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 0],
            ['name' => 'ক্লিক ৫-ঘাট মাল্টিপ্লাগ', 'category_index' => 1, 'cost' => 320.00, 'sell' => 420.00, 'stock' => 12.00, 'alert' => 3.00, 'unit_index' => 0],
            // Cosmetics
            ['name' => 'প্যারাসুট নারকেল তেল ২০০ মিলি', 'category_index' => 2, 'cost' => 140.00, 'sell' => 165.00, 'stock' => 30.00, 'alert' => 6.00, 'unit_index' => 0],
            ['name' => 'লাক্স গায়ে মাখার সাবান', 'category_index' => 2, 'cost' => 68.00, 'sell' => 75.00, 'stock' => 80.00, 'alert' => 20.00, 'unit_index' => 0],
            ['name' => 'সানসিল্ক শ্যাম্পু মিনি প্যাক', 'category_index' => 2, 'cost' => 180.00, 'sell' => 200.00, 'stock' => 100.00, 'alert' => 25.00, 'unit_index' => 4],
            ['name' => 'কোলগেট টুথপেস্ট ১৫০ গ্রাম', 'category_index' => 2, 'cost' => 135.00, 'sell' => 150.00, 'stock' => 40.00, 'alert' => 10.00, 'unit_index' => 0],
            // Clothing
            ['name' => 'এটিএম সুতি লুঙ্গি', 'category_index' => 3, 'cost' => 350.00, 'sell' => 450.00, 'stock' => 25.00, 'alert' => 5.00, 'unit_index' => 0],
            ['name' => 'জয়পুরী সুতি থ্রি-পিস', 'category_index' => 3, 'cost' => 850.00, 'sell' => 1150.00, 'stock' => 15.00, 'alert' => 3.00, 'unit_index' => 0],
        ];

        $users = User::where('role','admin')->get();

        foreach ($users as $user) {
            // Seed products for this user
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
