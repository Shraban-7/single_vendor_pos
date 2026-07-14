<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $suppliers = [
            // Garments & Textile Suppliers
            [
                'name' => 'Abdul Karim',
                'company_name' => 'Karim Textile Mills Ltd.',
                'product_category' => 'Garments & Textiles',
                'email' => 'karim.textile@gmail.com',
                'phone' => '+8801711234567',
                'phone_secondary' => '+8801811234567',
                'address' => 'House 45, Road 12, Sector 7, Uttara, Dhaka-1230',
                'opening_balance' => 150000.00,
                'notes' => 'Reliable supplier for cotton fabrics. 15 years of business relationship.',
            ],
            [
                'name' => 'Rahim Uddin',
                'company_name' => 'Rahim Garments Industries',
                'product_category' => 'Garments & Textiles',
                'email' => 'rahim.garments@gmail.com',
                'phone' => '+8801812345678',
                'phone_secondary' => null,
                'address' => '123 EPZ, Chattogram',
                'opening_balance' => 0.00,
                'notes' => 'Specializes in denim products.',
            ],
            [
                'name' => 'Fatema Begum',
                'company_name' => 'Fatema Silk House',
                'product_category' => 'Garments & Textiles',
                'email' => 'fatema.silk@gmail.com',
                'phone' => '+8801912345678',
                'phone_secondary' => '+8801612345678',
                'address' => 'Islampur Road, Islampur, Dhaka-1100',
                'opening_balance' => 75000.00,
                'notes' => 'Premium silk and saree supplier.',
            ],

            // Electronics & Electrical Suppliers
            [
                'name' => 'Mohammad Ali',
                'company_name' => 'Ali Electronics Trading',
                'product_category' => 'Electronics',
                'email' => 'ali.electronics@gmail.com',
                'phone' => '+8801712345679',
                'phone_secondary' => '+8801512345678',
                'address' => 'Shop 24, Gulistan Electronics Market, Dhaka-1000',
                'opening_balance' => 250000.00,
                'notes' => 'Authorized distributor of Samsung and Philips products.',
            ],
            [
                'name' => 'Kamal Hossain',
                'company_name' => 'Kamal Electric & Hardware',
                'product_category' => 'Electrical & Hardware',
                'email' => 'kamal.electric@gmail.com',
                'phone' => '+8801612345679',
                'phone_secondary' => null,
                'address' => 'Nawabpur Road, Dhaka-1100',
                'opening_balance' => 45000.00,
                'notes' => 'Wholesale electrical items and wiring materials.',
            ],
            [
                'name' => 'Shahin Alam',
                'company_name' => 'Shahin Mobile World',
                'product_category' => 'Mobile & Accessories',
                'email' => 'shahin.mobile@gmail.com',
                'phone' => '+8801912345679',
                'phone_secondary' => '+8801712345680',
                'address' => 'Bashundhara City, Level 4, Shop 156, Dhaka-1229',
                'opening_balance' => 500000.00,
                'notes' => 'Major mobile phone and accessories wholesaler.',
            ],

            // Groceries & Food Suppliers
            [
                'name' => 'Nurul Islam',
                'company_name' => 'Nurul General Store',
                'product_category' => 'Groceries & Food',
                'email' => 'nurul.store@gmail.com',
                'phone' => '+8801712345681',
                'phone_secondary' => '+8801812345681',
                'address' => 'New Market, Dhaka-1205',
                'opening_balance' => 85000.00,
                'notes' => 'Wholesale rice, dal, oil, and spices supplier.',
            ],
            [
                'name' => 'Jamal Uddin',
                'company_name' => 'Jamal Rice Mill & Traders',
                'product_category' => 'Groceries & Food',
                'email' => 'jamal.rice@gmail.com',
                'phone' => '+8801812345682',
                'phone_secondary' => null,
                'address' => 'Dinajpur Sadar, Dinajpur',
                'opening_balance' => 320000.00,
                'notes' => 'Premium Miniket and Nazirshail rice supplier from Dinajpur.',
            ],
            [
                'name' => 'Abdul Mannan',
                'company_name' => 'Mannan Oil Mills',
                'product_category' => 'Groceries & Food',
                'email' => 'mannan.oil@gmail.com',
                'phone' => '+8801512345679',
                'phone_secondary' => '+8801612345680',
                'address' => 'Mymensingh Road, Mymensingh',
                'opening_balance' => 180000.00,
                'notes' => 'Soybean and mustard oil manufacturer.',
            ],
            [
                'name' => 'Selina Akter',
                'company_name' => 'Selina Spices & Masala',
                'product_category' => 'Groceries & Food',
                'email' => 'selina.spices@gmail.com',
                'phone' => '+8801612345681',
                'phone_secondary' => null,
                'address' => 'Chawkbazar, Dhaka-1211',
                'opening_balance' => 65000.00,
                'notes' => 'Pure spices and masala powder supplier.',
            ],

            // Construction Materials
            [
                'name' => 'Habibur Rahman',
                'company_name' => 'Habib Cement & Steel',
                'product_category' => 'Construction Materials',
                'email' => 'habib.cement@gmail.com',
                'phone' => '+8801712345682',
                'phone_secondary' => '+8801912345680',
                'address' => 'Tongi Industrial Area, Gazipur',
                'opening_balance' => 750000.00,
                'notes' => 'Major cement and rod supplier. Bulk orders only.',
            ],
            [
                'name' => 'Anwar Hossain',
                'company_name' => 'Anwar Brick Field',
                'product_category' => 'Construction Materials',
                'email' => 'anwar.brick@gmail.com',
                'phone' => '+8801812345683',
                'phone_secondary' => null,
                'address' => 'Savar, Dhaka-1340',
                'opening_balance' => 420000.00,
                'notes' => 'First-class brick manufacturer.',
            ],
            [
                'name' => 'Mizanur Rahman',
                'company_name' => 'Mizan Tiles & Ceramics',
                'product_category' => 'Construction Materials',
                'email' => 'mizan.tiles@gmail.com',
                'phone' => '+8801912345681',
                'phone_secondary' => '+8801712345683',
                'address' => 'Bangaon, Gazipur',
                'opening_balance' => 280000.00,
                'notes' => 'Floor and wall tiles supplier.',
            ],

            // Pharmaceuticals & Medicine
            [
                'name' => 'Dr. Kamrul Hasan',
                'company_name' => 'Hasan Pharma Distributors',
                'product_category' => 'Pharmaceuticals',
                'email' => 'hasan.pharma@gmail.com',
                'phone' => '+8801712345684',
                'phone_secondary' => '+8801812345684',
                'address' => 'Motijheel C/A, Dhaka-1000',
                'opening_balance' => 550000.00,
                'notes' => 'Authorized distributor of Square and Beximco Pharma products.',
            ],
            [
                'name' => 'Rafiqul Islam',
                'company_name' => 'Rafiq Medical Store',
                'product_category' => 'Pharmaceuticals',
                'email' => 'rafiq.medical@gmail.com',
                'phone' => '+8801612345682',
                'phone_secondary' => null,
                'address' => 'Medical More, Rajshahi',
                'opening_balance' => 120000.00,
                'notes' => 'Wholesale medicine supplier for Rajshahi division.',
            ],

            // Agriculture & Fertilizer
            [
                'name' => 'Golam Mostafa',
                'company_name' => 'Mostafa Agro & Fertilizer',
                'product_category' => 'Agriculture',
                'email' => 'mostafa.agro@gmail.com',
                'phone' => '+8801712345685',
                'phone_secondary' => '+8801512345680',
                'address' => 'Bogura Sadar, Bogura',
                'opening_balance' => 95000.00,
                'notes' => 'Fertilizer, seeds, and agricultural tools supplier.',
            ],
            [
                'name' => 'Abdur Rashid',
                'company_name' => 'Rashid Fish & Poultry Farm',
                'product_category' => 'Agriculture',
                'email' => 'rashid.farm@gmail.com',
                'phone' => '+8801812345685',
                'phone_secondary' => null,
                'address' => 'Jessore Sadar, Jessore',
                'opening_balance' => 165000.00,
                'notes' => 'Fresh fish and poultry supplier.',
            ],

            // Furniture & Wood
            [
                'name' => 'Shamsul Haque',
                'company_name' => 'Shams Furniture Workshop',
                'product_category' => 'Furniture',
                'email' => 'shams.furniture@gmail.com',
                'phone' => '+8801912345682',
                'phone_secondary' => '+8801712345686',
                'address' => 'Jatrabari, Dhaka-1232',
                'opening_balance' => 210000.00,
                'notes' => 'Custom furniture manufacturer. Segun and mahogany wood specialist.',
            ],
            [
                'name' => 'Jahangir Alam',
                'company_name' => 'Jahangir Plywood & Timber',
                'product_category' => 'Furniture',
                'email' => 'jahangir.timber@gmail.com',
                'phone' => '+8801512345681',
                'phone_secondary' => null,
                'address' => 'Demra, Dhaka-1361',
                'opening_balance' => 340000.00,
                'notes' => 'Wholesale plywood and timber supplier.',
            ],

            // Cosmetics & Beauty
            [
                'name' => 'Taslima Khatun',
                'company_name' => 'Taslima Cosmetics House',
                'product_category' => 'Cosmetics & Beauty',
                'email' => 'taslima.cosmetics@gmail.com',
                'phone' => '+8801612345683',
                'phone_secondary' => '+8801812345686',
                'address' => 'Bongobazar, Dhaka-1211',
                'opening_balance' => 135000.00,
                'notes' => 'Wholesale cosmetics and beauty products.',
            ],

            // Stationery & Books
            [
                'name' => 'Nasir Uddin',
                'company_name' => 'Nasir Book & Stationery',
                'product_category' => 'Stationery & Books',
                'email' => 'nasir.books@gmail.com',
                'phone' => '+8801712345687',
                'phone_secondary' => '+8801912345683',
                'address' => 'Bangla Bazar, Dhaka-1100',
                'opening_balance' => 88000.00,
                'notes' => 'Wholesale books, notebooks, and office stationery.',
            ],

            // Footwear & Leather
            [
                'name' => 'Ibrahim Khalil',
                'company_name' => 'Khalil Leather & Footwear',
                'product_category' => 'Footwear & Leather',
                'email' => 'khalil.leather@gmail.com',
                'phone' => '+8801812345687',
                'phone_secondary' => null,
                'address' => 'Hazari Bagh, Dhaka-1209',
                'opening_balance' => 275000.00,
                'notes' => 'Leather shoes and bags manufacturer.',
            ],

            // Packaging Materials
            [
                'name' => 'Masud Rana',
                'company_name' => 'Masud Packaging Solutions',
                'product_category' => 'Packaging Materials',
                'email' => 'masud.packaging@gmail.com',
                'phone' => '+8801912345684',
                'phone_secondary' => '+8801612345684',
                'address' => 'Keraniganj, Dhaka-1310',
                'opening_balance' => 195000.00,
                'notes' => 'Carton boxes, poly bags, and packaging materials supplier.',
            ],

            // Plastic & Rubber
            [
                'name' => 'Rafiq Ahmed',
                'company_name' => 'Rafiq Plastic Industries',
                'product_category' => 'Plastic & Rubber',
                'email' => 'rafiq.plastic@gmail.com',
                'phone' => '+8801512345682',
                'phone_secondary' => null,
                'address' => 'Rupnagar, Mirpur, Dhaka-1216',
                'opening_balance' => 155000.00,
                'notes' => 'Plastic containers and household items manufacturer.',
            ],

            // Transport & Logistics
            [
                'name' => 'Abdul Malek',
                'company_name' => 'Malek Transport & Logistics',
                'product_category' => 'Transport & Logistics',
                'email' => 'malek.transport@gmail.com',
                'phone' => '+8801712345688',
                'phone_secondary' => '+8801812345688',
                'address' => 'Sayedabad Bus Terminal, Dhaka-1000',
                'opening_balance' => 480000.00,
                'notes' => 'Truck rental and goods transport service provider.',
            ],
        ];

        foreach ($suppliers as $index => $supplierData) {
            $supplierCode = 'SUP-' . $userId . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Supplier::create([
                'user_id' => $userId,
                'name' => $supplierData['name'],
                'company_name' => $supplierData['company_name'],
                'product_category' => $supplierData['product_category'],
                'email' => $supplierData['email'],
                'phone' => $supplierData['phone'],
                'phone_secondary' => $supplierData['phone_secondary'],
                'address' => $supplierData['address'],
                'supplier_code' => $supplierCode,
                'opening_balance' => $supplierData['opening_balance'],
                'current_balance' => $supplierData['opening_balance'],
                'total_purchases' => 0.00,
                'total_paid' => 0.00,
                'is_active' => true,
                'notes' => $supplierData['notes'],
            ]);
        }
    }
}
