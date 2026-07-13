<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $rootIcons = [
            'Men' => 'fa-solid fa-person',
            'Women' => 'fa-solid fa-person-dress',
            'Kids' => 'fa-solid fa-child',
            'Footwear' => 'fa-solid fa-shoe-prints',
            'Fashion Accessories' => 'fa-solid fa-gem',
            'Winter Collection' => 'fa-solid fa-snowflake',
            'Islamic Fashion' => 'fa-solid fa-mosque',
            'Summer Collection' => 'fa-solid fa-sun',
            'Bags & Luggage' => 'fa-solid fa-suitcase',
        ];

        $categoryIcons = [
            'Panjabi' => 'fa-solid fa-shirt',
            'Shirt' => 'fa-solid fa-shirt',
            'T-Shirt' => 'fa-solid fa-shirt',
            'Pant' => 'fa-solid fa-user-tie',

            'Saree' => 'fa-solid fa-person-dress',
            'Salwar Kameez' => 'fa-solid fa-person-dress',
            'Kurti' => 'fa-solid fa-shirt',

            'Boys' => 'fa-solid fa-child',
            'Girls' => 'fa-solid fa-child-dress',

            'Men Shoes' => 'fa-solid fa-shoe-prints',
            'Women Shoes' => 'fa-solid fa-shoe-prints',

            'Men Accessories' => 'fa-solid fa-gem',
            'Women Accessories' => 'fa-solid fa-gem',

            'Men Islamic Wear' => 'fa-solid fa-mosque',
            'Women Islamic Wear' => 'fa-solid fa-mosque',
            'Islamic Accessories' => 'fa-solid fa-star-and-crescent',

            'Men Summer Wear' => 'fa-solid fa-shirt',
            'Women Summer Wear' => 'fa-solid fa-person-dress',
            'Kids Summer Wear' => 'fa-solid fa-child',
            'Summer Accessories' => 'fa-solid fa-glasses',

            'Backpack' => 'fa-solid fa-backpack',
            'Handbag' => 'fa-solid fa-bag-shopping',
            'Travel Bags' => 'fa-solid fa-suitcase-rolling',
            'Wallets & Holders' => 'fa-solid fa-wallet',
        ];

        $categories = [
            'Men' => [
                'Panjabi' => [
                    'Casual Panjabi',
                    'Cotton Panjabi',
                    'Printed Panjabi',
                    'Embroidered Panjabi',
                    'Silk Panjabi',
                    'Linen Panjabi',
                    'Party Panjabi'
                ],

                'Shirt' => [
                    'Formal Shirt',
                    'Casual Shirt',
                    'Half Sleeve Shirt',
                    'Full Sleeve Shirt',
                ],

                'T-Shirt' => [
                    'Basic T-Shirt',
                    'Polo T-Shirt',
                    'Graphic T-Shirt',
                    'Oversized T-Shirt',
                    'V-Neck T-Shirt',
                    'Round Neck T-Shirt',
                    'Drop Shoulder T-Shirt',
                ],

                'Pant' => [
                    'Formal Pant',
                    'Jeans',
                    'Chino Pant',
                    'Cargo Pant',
                    'Jogger Pant',
                    'Shorts',
                    'Trousers',
                    'Track Pant',
                ],
            ],

            'Women' => [
                'Saree' => [
                    'Cotton Saree',
                    'Jamdani Saree',
                    'Silk Saree',
                    'Party Saree',
                    'Katan Saree',
                    'Printed Saree',
                    'Embroidered Saree',
                    'Banarasi Saree'
                ],

                'Salwar Kameez' => [
                    'Printed',
                    'Embroidered',
                    'Readymade',
                    'Unstitched',
                    'Pakistani',
                ],

                'Kurti' => [
                    'Casual Kurti',
                    'Long Kurti',
                    'Short Kurti',
                    'Printed Kurti',
                    'Embroidered Kurti',
                    'Party Kurti',
                    'Festive Kurti'
                ],
            ],

            'Kids' => [
                'Boys' => [
                    'Panjabi',
                    'Shirt',
                    'T-Shirt',
                    'Jeans',
                    'Shorts',
                ],

                'Girls' => [
                    'Frock',
                    'Party Dress',
                    'Skirt',
                    'Leggings',
                    'Tops',
                ],
            ],

            'Footwear' => [
                'Mens Footwear' => [
                    'Sneaker',
                    'Loafer',
                    'Formal Shoes',
                    'Sandal',
                    'Boot',
                    'Sports Shoes',
                    'Slip-On',
                    'Casual Shoes',
                ],

                'Womens Footwear' => [
                    'Heel',
                    'Flat Sandal',
                    'Sneaker',
                    'Boot',
                    'Loafer',
                    'Wedge',
                    'Sports Shoes',
                    'Slip-On',
                ],
            ],

            'Accessories' => [
                'Men Accessories' => [
                    'Wallet',
                    'Belt',
                    'Watch',
                    'Cap',
                    'Sunglasses',
                    'Tie',
                    'Cufflinks',
                    'Bracelet',
                ],

                'Women Accessories' => [
                    'Jewelry',
                    'Handbag',
                    'Hair Accessories',
                    'Watch',
                    'Sunglasses',
                    'Scarf',
                    'Belt',
                    'Hat',
                ],
            ],

            'Winter Collection' => [

                'Men Winter Wear' => [
                    'Hoodie',
                    'Sweater',
                    'Jacket',
                    'Blazer',
                    'Coat',
                    'Cardigan',
                    'Thermal Wear',
                    'Winter Tracksuit',
                ],

                'Women Winter Wear' => [
                    'Hoodie',
                    'Sweater',
                    'Jacket',
                    'Coat',
                    'Cardigan',
                    'Shawl',
                    'Thermal Wear',
                    'Winter Dress',
                ],

                'Kids Winter Wear' => [
                    'Kids Hoodie',
                    'Kids Sweater',
                    'Kids Jacket',
                    'Kids Coat',
                    'Kids Thermal Wear',
                    'Kids Winter Set',
                ],

                'Winter Accessories' => [
                    'Muffler',
                    'Scarf',
                    'Winter Cap',
                    'Beanie',
                    'Gloves',
                    'Ear Muff',
                    'Winter Socks',
                ],
            ],

            'Islamic Fashion' => [

                'Men Islamic Wear' => [
                    'Jubba',
                    'Thobe',
                    'Arabic Thobe',
                    'Emirati Thobe',
                    'Saudi Thobe',
                    'Panjabi',
                    'Prayer Cap',
                    'Imama',
                ],

                'Women Islamic Wear' => [
                    'Abaya',
                    'Dubai Abaya',
                    'Saudi Abaya',
                    'Front Open Abaya',
                    'Butterfly Abaya',
                    'Khimar',
                    'Hijab',
                    'Niqab',
                    'Jilbab',
                    'Prayer Dress',
                ],

                'Islamic Accessories' => [
                    'Prayer Mat',
                    'Tasbih',
                    'Prayer Cap',
                    'Hijab Pin',
                    'Sleeves',
                    'Undercap',
                ],
            ],
            'Bags & Luggage' => [
                'Backpack' => [
                    'School Backpack',
                    'College Backpack',
                    'Travel Backpack',
                    'Laptop Backpack',
                ],

                'Handbag' => [
                    'Casual Handbag',
                    'Party Handbag',
                    'Shoulder Bag',
                    'Tote Bag',
                ],

                'Travel Bags' => [
                    'Duffel Bag',
                    'Trolley Bag',
                    'Suitcase',
                    'Cabin Luggage',
                ],

                'Wallets & Holders' => [
                    'Wallet',
                    'Card Holder',
                    'Passport Holder',
                ],
            ],

            'Summer Collection' => [
                'Men Summer Wear' => [
                    'Polo T-Shirt',
                    'Linen Shirt',
                    'Bermuda Shorts',
                    'Summer Jogger',
                    'Cotton T-Shirt',
                    'Summer Vest',
                ],

                'Women Summer Wear' => [
                    'Summer Dress',
                    'Maxi Dress',
                    'Tank Top',
                    'Summer Kurti',
                    'Palazzo',
                    'Sun Dress',
                ],

                'Kids Summer Wear' => [
                    'Kids T-Shirt',
                    'Kids Shorts',
                    'Summer Frock',
                    'Summer Top',
                ],

                'Summer Accessories' => [
                    'Sunglasses',
                    'Sun Hat',
                    'Beach Sandals',
                    'Flip Flops',
                ],
            ],
        ];

        $inactiveDepartments = ['Islamic Fashion'];
        $sortOrder = 1;

        foreach ($categories as $departmentName => $departmentCategories) {

            $departmentData = [
                'name' => $departmentName,
                'icon' => $rootIcons[$departmentName] ?? null,
                'sort_order' => $sortOrder++,
            ];

            if (in_array($departmentName, $inactiveDepartments)) {
                $departmentData['is_active'] = false;
            }

            $department = Category::updateOrCreate(
                ['slug' => Str::slug($departmentName)],
                $departmentData
            );

            foreach ($departmentCategories as $categoryName => $subCategories) {

                $category = Category::updateOrCreate(
                    ['slug' => Str::slug($departmentName . '-' . $categoryName)],
                    [
                        'parent_id' => $department->id,
                        'name' => $categoryName,
                        'icon' => $categoryIcons[$categoryName] ?? null,
                    ]
                );

                foreach ($subCategories as $subCategoryName) {

                    Category::updateOrCreate(
                        [
                            'slug' => Str::slug(
                                $departmentName . '-' .
                                    $categoryName . '-' .
                                    $subCategoryName
                            )
                        ],
                        [
                            'parent_id' => $category->id,
                            'name' => $subCategoryName,
                            'icon' => null,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Run the database seeds.
     */
    public function runOld(): void
    {
        // Clear existing categories to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Category structure configuration
        $categories = $this->getCategoryStructure();

        $sortOrder = 1;
        foreach ($categories as $categoryData) {
            $this->createCategory($categoryData, null, $sortOrder++);
        }
    }

    /**
     * Get the category structure - can be modified to use different data sources.
     *
     * @return array
     */
    private function getCategoryStructure(): array
    {
        return [
            [
                'name' => 'Men',
                'icon' => 'fas fa-male',
                'is_featured' => true,
                'children' => [
                    [
                        'name' => 'T-Shirts',
                        'icon' => 'fas fa-tshirt',
                        'children' => [
                            ['name' => 'Round Neck', 'icon' => 'fas fa-circle'],
                            ['name' => 'V-Neck', 'icon' => 'fas fa-v'],
                            ['name' => 'Polo', 'icon' => 'fas fa-tshirt'],
                            ['name' => 'Henley', 'icon' => 'fas fa-tshirt'],
                        ],
                    ],
                    [
                        'name' => 'Shirts',
                        'icon' => 'fas fa-shirt',
                        'children' => [
                            ['name' => 'Casual Shirts', 'icon' => 'fas fa-shirt'],
                            ['name' => 'Formal Shirts', 'icon' => 'fas fa-user-tie'],
                            ['name' => 'Denim Shirts', 'icon' => 'fas fa-shirt'],
                        ],
                    ],
                    [
                        'name' => 'Pants',
                        'icon' => 'fas fa-socks',
                        'children' => [
                            ['name' => 'Jeans', 'icon' => 'fas fa-socks'],
                            ['name' => 'Chinos', 'icon' => 'fas fa-socks'],
                            ['name' => 'Formal Pants', 'icon' => 'fas fa-user-tie'],
                            ['name' => 'Joggers', 'icon' => 'fas fa-running'],
                        ],
                    ],
                    [
                        'name' => 'Panjabi',
                        'icon' => 'fas fa-vest',
                        'children' => [
                            ['name' => 'Cotton Panjabi', 'icon' => 'fas fa-vest'],
                            ['name' => 'Silk Panjabi', 'icon' => 'fas fa-star'],
                            ['name' => 'Embroidered Panjabi', 'icon' => 'fas fa-gem'],
                        ],
                    ],
                    ['name' => 'Jackets & Hoodies', 'icon' => 'fas fa-vest'],
                    ['name' => 'Shorts', 'icon' => 'fas fa-socks'],
                    ['name' => 'Activewear', 'icon' => 'fas fa-dumbbell'],
                ],
            ],
            [
                'name' => 'Women',
                'icon' => 'fas fa-female',
                'is_featured' => true,
                'children' => [
                    [
                        'name' => 'Saree',
                        'icon' => 'fas fa-person-dress',
                        'children' => [
                            ['name' => 'Cotton Saree', 'icon' => 'fas fa-person-dress'],
                            ['name' => 'Silk Saree', 'icon' => 'fas fa-star'],
                            ['name' => 'Jamdani', 'icon' => 'fas fa-gem'],
                            ['name' => 'Katan', 'icon' => 'fas fa-gem'],
                        ],
                    ],
                    [
                        'name' => 'Salwar Kameez',
                        'icon' => 'fas fa-person-dress',
                        'children' => [
                            ['name' => 'Unstitched', 'icon' => 'fas fa-scissors'],
                            ['name' => 'Ready-Made', 'icon' => 'fas fa-check'],
                            ['name' => 'Designer', 'icon' => 'fas fa-palette'],
                        ],
                    ],
                    [
                        'name' => 'Kurti',
                        'icon' => 'fas fa-person-dress',
                        'children' => [
                            ['name' => 'Short Kurti', 'icon' => 'fas fa-person-dress'],
                            ['name' => 'Long Kurti', 'icon' => 'fas fa-person-dress'],
                            ['name' => 'A-Line Kurti', 'icon' => 'fas fa-person-dress'],
                        ],
                    ],
                    ['name' => 'Tops & Tunics', 'icon' => 'fas fa-shirt'],
                    ['name' => 'Palazzo & Pants', 'icon' => 'fas fa-socks'],
                    ['name' => 'Western Wear', 'icon' => 'fas fa-hat-cowboy'],
                    ['name' => 'Abaya & Hijab', 'icon' => 'fas fa-person-dress'],
                ],
            ],
            [
                'name' => 'Kids',
                'icon' => 'fas fa-child',
                'is_featured' => true,
                'children' => [
                    [
                        'name' => 'Boys',
                        'icon' => 'fas fa-child',
                        'children' => [
                            ['name' => 'T-Shirts', 'icon' => 'fas fa-tshirt'],
                            ['name' => 'Shirts', 'icon' => 'fas fa-shirt'],
                            ['name' => 'Pants', 'icon' => 'fas fa-socks'],
                            ['name' => 'Panjabi', 'icon' => 'fas fa-vest'],
                        ],
                    ],
                    [
                        'name' => 'Girls',
                        'icon' => 'fas fa-child-dress',
                        'children' => [
                            ['name' => 'Frocks', 'icon' => 'fas fa-person-dress'],
                            ['name' => 'Salwar Kameez', 'icon' => 'fas fa-person-dress'],
                            ['name' => 'Tops', 'icon' => 'fas fa-shirt'],
                        ],
                    ],
                    ['name' => 'Baby (0-2 Years)', 'icon' => 'fas fa-baby'],
                ],
            ],
            [
                'name' => 'Accessories',
                'icon' => 'fas fa-watch',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Watches', 'icon' => 'fas fa-clock'],
                    ['name' => 'Bags', 'icon' => 'fas fa-bag-shopping'],
                    ['name' => 'Belts', 'icon' => 'fas fa-grip-lines'],
                    ['name' => 'Wallets', 'icon' => 'fas fa-wallet'],
                    ['name' => 'Caps & Hats', 'icon' => 'fas fa-hat-cowboy'],
                    ['name' => 'Sunglasses', 'icon' => 'fas fa-glasses'],
                    ['name' => 'Jewelry', 'icon' => 'fas fa-gem'],
                ],
            ],
            [
                'name' => 'Footwear',
                'icon' => 'fas fa-shoe-prints',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Sneakers', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Sandals', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Formal Shoes', 'icon' => 'fas fa-user-tie'],
                    ['name' => 'Sports Shoes', 'icon' => 'fas fa-running'],
                    ['name' => 'Loafers', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Heels', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Flats', 'icon' => 'fas fa-shoe-prints'],
                ],
            ],
        ];
    }

    /**
     * Create a category using the factory (dynamic approach).
     *
     * @param array $data
     * @param int|null $parentId
     * @param int $sortOrder
     * @param string|null $parentSlug
     * @return void
     */
    private function createCategory(array $data, ?int $parentId, int $sortOrder, ?string $parentSlug = null): void
    {
        $children = $data['children'] ?? [];
        unset($data['children']);

        // Create unique slug by incorporating parent slug if exists
        $slug = \Illuminate\Support\Str::slug($data['name']);
        if ($parentSlug) {
            $slug = $parentSlug . '-' . $slug;
        }

        // Use the factory to create the category dynamically
        $factory = Category::factory()->state([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        if (isset($data['icon'])) {
            $factory = $factory->withIcon($data['icon']);
        }

        if ($data['is_featured'] ?? false) {
            $factory = $factory->featured();
        }

        $category = $factory->active()->create([
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
        ]);

        $childSortOrder = 1;
        foreach ($children as $childData) {
            $this->createCategory($childData, $category->id, $childSortOrder++, $slug);
        }
    }
}
