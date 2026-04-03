<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
