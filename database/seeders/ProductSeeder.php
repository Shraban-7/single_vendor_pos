<?php

namespace Database\Seeders;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private $brands = [
        'men' => ['Aarong', 'Panjabi', 'Sailor', 'Gentle Park', 'Yellow', 'Cats Eye', 'Ecstasy', 'Easy', 'Richman', 'Deshal'],
        'women' => ['Aarong', 'Kay Kraft', 'Rang Bangladesh', 'Bishworang', 'Anjans', 'Lubnan', 'Westecs', 'Infinity', 'Zara', 'Richman'],
        'kids' => ['Aarong Kids', 'Kids Fashion', 'Yellow Kids', 'Cute Collection', 'Happy Kids'],
        'accessories' => ['Bata', 'Lotto', 'Apex', 'Bay', 'Fashion Point'],
        'footwear' => ['Bata', 'Apex', 'Lotto', 'Sprint', 'Bay'],
    ];

    private $materials = [
        'tshirt' => ['100% Cotton', 'Cotton Blend', 'Polyester Blend', 'Premium Cotton'],
        'shirt' => ['Cotton', '100% Cotton', 'Cotton Twill', 'Denim', 'Linen Blend'],
        'pants' => ['Denim', 'Cotton Twill', 'Chino Cotton', 'Polyester Blend'],
        'panjabi' => ['Cotton', 'Premium Cotton', 'Silk', 'Semi Silk', 'Voile'],
        'saree' => ['Cotton', 'Silk', 'Georgette', 'Jamdani Cotton', 'Katan Silk'],
        'salwar' => ['Cotton', 'Lawn Cotton', 'Georgette', 'Silk Blend'],
        'kurti' => ['Cotton', 'Rayon', 'Georgette', 'Cotton Blend'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::with('children.children')->get();

        foreach ($categories as $category) {
            // Create products for main categories with children
            if ($category->children->isNotEmpty()) {
                foreach ($category->children as $subCategory) {
                    // For subcategories with children (like T-Shirts -> Round Neck)
                    if ($subCategory->children->isNotEmpty()) {
                        foreach ($subCategory->children as $subSubCategory) {
                            $this->createProductsForCategory($subSubCategory, 3); // 3 products per sub-subcategory
                        }
                    } else {
                        // For subcategories without children
                        $this->createProductsForCategory($subCategory, 5); // 5 products per subcategory
                    }
                }
            }
        }
    }

    /**
     * Create products for a specific category
     */
    private function createProductsForCategory(Category $category, int $count): void
    {
        $categoryName = strtolower($category->name);
        $parentName = $category->parent ? strtolower($category->parent->name) : '';
        $grandParentName = $category->parent && $category->parent->parent ? strtolower($category->parent->parent->name) : '';

        for ($i = 1; $i <= $count; $i++) {
            $productData = $this->generateProductData($category, $i);

            $product = Product::create($productData);

            // Create variants for the product
            $this->createProductVariants($product, $categoryName, $grandParentName);
        }
    }

    /**
     * Generate product data based on category
     */
    private function generateProductData(Category $category, int $index): array
    {
        $categoryName = strtolower($category->name);
        $parentName = $category->parent ? strtolower($category->parent->name) : '';
        $grandParentName = $category->parent && $category->parent->parent ? strtolower($category->parent->parent->name) : '';

        $productName = $this->getProductName($categoryName, $parentName, $grandParentName, $index);
        $pricing = $this->getPricing($categoryName, $parentName, $grandParentName);
        $brand = $this->getBrand($grandParentName ?: $parentName ?: $categoryName);
        $material = $this->getMaterial($parentName ?: $categoryName);

        $isOnSale = rand(0, 100) < 30; // 30% chance of being on sale
        $isFeatured = rand(0, 100) < 20; // 20% chance of being featured
        $isNewArrival = rand(0, 100) < 25; // 25% chance of being new arrival
        $isBestSeller = rand(0, 100) < 15; // 15% chance of being best seller

        return [
            'name' => $productName,
            'slug' => Str::slug($productName . '-' . uniqid()),
            'sku' => strtoupper(substr($categoryName, 0, 3)) . rand(1000, 9999),
            'short_description' => $this->getShortDescription($categoryName, $brand, $material),
            'description' => $this->getDescription($productName, $material, $brand),
            'price' => $pricing['price'],
            'compare_price' => $isOnSale ? $pricing['compare_price'] : null,
            'cost_price' => $pricing['cost_price'],
            'category_id' => $category->id,
            'brand' => $brand,
            'material' => $material,
            'fit_type' => $this->getFitType($categoryName, $parentName),
            'pattern' => $this->getPattern(),
            'occasion' => $this->getOccasion($categoryName, $parentName),
            'stock_in' => rand(50, 200),
            'stock_out' => rand(0, 20),
            'low_stock_threshold' => 10,
            'weight' => rand(200, 800),
            'is_active' => true,
            'is_featured' => $isFeatured,
            'is_new_arrival' => $isNewArrival,
            'is_best_seller' => $isBestSeller,
            'is_on_sale' => $isOnSale,
            'average_rating' => rand(35, 50) / 10, // 3.5 to 5.0
            'review_count' => rand(5, 100),
            'view_count' => rand(50, 1000),
            'meta_title' => $productName . ' - Buy Online in Bangladesh',
            'meta_description' => "Shop {$productName} from {$brand}. {$this->getShortDescription($categoryName,$brand,$material)}",
            'tags' => json_encode($this->getTags($categoryName, $parentName, $brand)),
        ];
    }

    /**
     * Get product name based on category
     */
    private function getProductName(string $category, string $parent, string $grandParent, int $index): string
    {
        $names = [
            // Men's T-Shirts
            'round neck' => [
                'Premium Cotton Round Neck T-Shirt',
                'Classic Solid Round Neck Tee',
                'Vintage Print Round Neck T-Shirt',
                'Slim Fit Round Neck T-Shirt',
            ],
            'v-neck' => [
                'Stylish V-Neck Cotton T-Shirt',
                'Classic V-Neck Solid Tee',
                'Premium V-Neck T-Shirt',
            ],
            'polo' => [
                'Classic Pique Polo Shirt',
                'Premium Cotton Polo T-Shirt',
                'Striped Polo Shirt',
            ],
            'henley' => [
                'Long Sleeve Henley T-Shirt',
                'Buttoned Henley Casual Tee',
            ],

            // Men's Shirts
            'casual shirts' => [
                'Cotton Casual Shirt',
                'Slim Fit Casual Shirt',
                'Checked Casual Shirt',
                'Printed Casual Shirt',
                'Oxford Cotton Casual Shirt',
            ],
            'formal shirts' => [
                'Formal Cotton Shirt',
                'Slim Fit Formal Shirt',
                'Regular Fit Formal Shirt',
                'White Formal Shirt',
                'Blue Formal Shirt',
            ],
            'denim shirts' => [
                'Classic Denim Shirt',
                'Light Blue Denim Shirt',
                'Dark Denim Casual Shirt',
            ],

            // Men's Pants
            'jeans' => [
                'Slim Fit Stretch Jeans',
                'Regular Fit Blue Jeans',
                'Black Denim Jeans',
                'Comfort Fit Jeans',
                'Ripped Slim Fit Jeans',
            ],
            'chinos' => [
                'Slim Fit Chino Pants',
                'Stretch Chino Trousers',
                'Classic Fit Chino Pants',
                'Casual Chino Pants',
            ],
            'formal pants' => [
                'Formal Dress Pants',
                'Slim Fit Formal Trousers',
                'Regular Fit Formal Pants',
                'Black Formal Trousers',
            ],
            'joggers' => [
                'Comfort Fit Jogger Pants',
                'Athletic Joggers',
                'Slim Fit Joggers',
            ],

            // Men's Panjabi
            'cotton panjabi' => [
                'Premium Cotton Panjabi',
                'Hand Block Printed Panjabi',
                'Solid Color Cotton Panjabi',
                'Traditional Cotton Panjabi',
            ],
            'silk panjabi' => [
                'Premium Silk Panjabi',
                'Designer Silk Panjabi',
                'Festive Silk Panjabi',
            ],
            'embroidered panjabi' => [
                'Hand Embroidered Panjabi',
                'Festive Embroidered Panjabi',
                'Designer Embroidered Panjabi',
            ],

            // Women's Saree
            'cotton saree' => [
                'Handloom Cotton Saree',
                'Tant Cotton Saree',
                'Pure Cotton Saree',
                'Block Printed Cotton Saree',
            ],
            'silk saree' => [
                'Pure Silk Saree',
                'Designer Silk Saree',
                'Banarasi Silk Saree',
                'Party Wear Silk Saree',
            ],
            'jamdani' => [
                'Traditional Jamdani Saree',
                'Handwoven Jamdani Saree',
                'Designer Jamdani Saree',
            ],
            'katan' => [
                'Pure Katan Silk Saree',
                'Designer Katan Saree',
                'Festive Katan Saree',
            ],

            // Women's Salwar Kameez
            'unstitched' => [
                'Unstitched Lawn Three Piece',
                'Cotton Unstitched Salwar Set',
                'Designer Unstitched Three Piece',
                'Premium Unstitched Fabric Set',
            ],
            'ready-made' => [
                'Ready Made Salwar Kameez',
                'Cotton Salwar Suit',
                'Designer Salwar Kameez',
                'Party Wear Salwar Set',
            ],
            'designer' => [
                'Designer Embroidered Salwar',
                'Premium Designer Three Piece',
                'Festive Designer Salwar Kameez',
            ],

            // Women's Kurti
            'short kurti' => [
                'Cotton Short Kurti',
                'Printed Short Kurti',
                'Designer Short Kurti',
            ],
            'long kurti' => [
                'Long Cotton Kurti',
                'Designer Long Kurti',
                'Printed Long Kurti',
                'Casual Long Kurti',
            ],
            'a-line kurti' => [
                'A-Line Cotton Kurti',
                'Flared A-Line Kurti',
                'Designer A-Line Kurti',
            ],

            // Kids
            't-shirts' => [
                'Kids Cotton T-Shirt',
                'Printed Kids Tee',
                'Cartoon Print T-Shirt',
                'Colorful Kids T-Shirt',
            ],
            'shirts' => [
                'Kids Casual Shirt',
                'Checked Kids Shirt',
                'Cotton Kids Shirt',
            ],
            'pants' => [
                'Kids Casual Pants',
                'Kids Jeans',
                'Comfortable Kids Trousers',
            ],
            'panjabi' => [
                'Kids Cotton Panjabi',
                'Designer Kids Panjabi',
            ],
            'frocks' => [
                'Girls Party Frock',
                'Cotton Frock for Girls',
                'Designer Girls Frock',
                'Printed Girls Frock',
            ],

            // Accessories
            'watches' => [
                'Classic Analog Watch',
                'Digital Sports Watch',
                'Leather Strap Watch',
                'Metal Band Watch',
            ],
            'bags' => [
                'Leather Messenger Bag',
                'Canvas Backpack',
                'Office Laptop Bag',
                'Casual Sling Bag',
            ],
            'belts' => [
                'Genuine Leather Belt',
                'Casual Canvas Belt',
                'Formal Leather Belt',
            ],
            'wallets' => [
                'Leather Bi-Fold Wallet',
                'Genuine Leather Wallet',
                'RFID Protection Wallet',
            ],

            // Footwear
            'sneakers' => [
                'Casual Canvas Sneakers',
                'Sports Running Sneakers',
                'White Canvas Sneakers',
                'Comfortable Casual Sneakers',
            ],
            'sandals' => [
                'Comfortable Summer Sandals',
                'Casual Leather Sandals',
                'Open Toe Sandals',
            ],
            'formal shoes' => [
                'Formal Leather Shoes',
                'Oxford Formal Shoes',
                'Black Formal Shoes',
            ],
        ];

        $key = $category;
        if (!isset($names[$key])) {
            // Fallback names
            return ucwords($category) . " Style " . ['Classic', 'Premium', 'Designer', 'Modern', 'Trendy'][($index - 1) % 5];
        }

        $nameList = $names[$key];
        return $nameList[($index - 1) % count($nameList)];
    }

    /**
     * Get pricing based on category (BD market prices in BDT)
     */
    private function getPricing(string $category, string $parent, string $grandParent): array
    {
        $priceRanges = [
            // Men
            'round neck' => ['min' => 350, 'max' => 800],
            'v-neck' => ['min' => 400, 'max' => 850],
            'polo' => ['min' => 600, 'max' => 1500],
            'henley' => ['min' => 500, 'max' => 1200],
            'casual shirts' => ['min' => 800, 'max' => 2500],
            'formal shirts' => ['min' => 1200, 'max' => 3500],
            'denim shirts' => ['min' => 1500, 'max' => 3000],
            'jeans' => ['min' => 1200, 'max' => 3500],
            'chinos' => ['min' => 1500, 'max' => 3000],
            'formal pants' => ['min' => 1500, 'max' => 4000],
            'joggers' => ['min' => 800, 'max' => 2000],
            'cotton panjabi' => ['min' => 1200, 'max' => 3000],
            'silk panjabi' => ['min' => 2500, 'max' => 8000],
            'embroidered panjabi' => ['min' => 2000, 'max' => 6000],

            // Women
            'cotton saree' => ['min' => 1500, 'max' => 5000],
            'silk saree' => ['min' => 3000, 'max' => 15000],
            'jamdani' => ['min' => 5000, 'max' => 25000],
            'katan' => ['min' => 4000, 'max' => 20000],
            'unstitched' => ['min' => 800, 'max' => 3500],
            'ready-made' => ['min' => 1500, 'max' => 5000],
            'designer' => ['min' => 3000, 'max' => 8000],
            'short kurti' => ['min' => 600, 'max' => 1800],
            'long kurti' => ['min' => 800, 'max' => 2500],
            'a-line kurti' => ['min' => 700, 'max' => 2000],

            // Kids
            't-shirts' => ['min' => 250, 'max' => 600],
            'shirts' => ['min' => 400, 'max' => 1200],
            'pants' => ['min' => 500, 'max' => 1500],
            'panjabi' => ['min' => 800, 'max' => 2000],
            'frocks' => ['min' => 500, 'max' => 1800],

            // Accessories & Footwear
            'watches' => ['min' => 800, 'max' => 5000],
            'bags' => ['min' => 600, 'max' => 3500],
            'belts' => ['min' => 300, 'max' => 1200],
            'wallets' => ['min' => 400, 'max' => 1800],
            'sneakers' => ['min' => 1200, 'max' => 4000],
            'sandals' => ['min' => 500, 'max' => 2000],
            'formal shoes' => ['min' => 1500, 'max' => 5000],
        ];

        $range = $priceRanges[$category] ?? $priceRanges[$parent] ?? ['min' => 500, 'max' => 2000];

        $price = rand($range['min'], $range['max']);
        $costPrice = $price * 0.6; // 40% margin
        $comparePrice = $price * rand(115, 140) / 100; // 15-40% discount

        return [
            'price' => $price,
            'cost_price' => round($costPrice, 2),
            'compare_price' => round($comparePrice, 2),
        ];
    }

    /**
     * Get brand based on category
     */
    private function getBrand(string $category): string
    {
        $categoryKey = 'men';

        if (str_contains($category, 'women') || str_contains($category, 'saree') || str_contains($category, 'salwar') || str_contains($category, 'kurti')) {
            $categoryKey = 'women';
        } elseif (str_contains($category, 'kids') || str_contains($category, 'boys') || str_contains($category, 'girls') || str_contains($category, 'baby')) {
            $categoryKey = 'kids';
        } elseif (str_contains($category, 'accessories') || str_contains($category, 'watch') || str_contains($category, 'bag') || str_contains($category, 'belt') || str_contains($category, 'wallet')) {
            $categoryKey = 'accessories';
        } elseif (str_contains($category, 'footwear') || str_contains($category, 'shoe') || str_contains($category, 'sandal') || str_contains($category, 'sneaker')) {
            $categoryKey = 'footwear';
        }

        $brands = $this->brands[$categoryKey] ?? $this->brands['men'];
        return $brands[array_rand($brands)];
    }

    /**
     * Get material based on category
     */
    private function getMaterial(string $category): string
    {
        $materialKey = null;

        if (str_contains($category, 't-shirt') || str_contains($category, 'tshirt') || str_contains($category, 'tee')) {
            $materialKey = 'tshirt';
        } elseif (str_contains($category, 'shirt')) {
            $materialKey = 'shirt';
        } elseif (str_contains($category, 'pants') || str_contains($category, 'jeans') || str_contains($category, 'chinos') || str_contains($category, 'joggers')) {
            $materialKey = 'pants';
        } elseif (str_contains($category, 'panjabi')) {
            $materialKey = 'panjabi';
        } elseif (str_contains($category, 'saree')) {
            $materialKey = 'saree';
        } elseif (str_contains($category, 'salwar')) {
            $materialKey = 'salwar';
        } elseif (str_contains($category, 'kurti')) {
            $materialKey = 'kurti';
        }

        if ($materialKey && isset($this->materials[$materialKey])) {
            return $this->materials[$materialKey][array_rand($this->materials[$materialKey])];
        }

        return 'Cotton';
    }

    /**
     * Get fit type
     */
    private function getFitType(string $category, string $parent): ?string
    {
        $wearCategories = ['shirt', 't-shirt', 'tshirt', 'polo', 'henley', 'panjabi', 'pants', 'jeans', 'chinos'];

        foreach ($wearCategories as $wear) {
            if (str_contains($category, $wear) || str_contains($parent, $wear)) {
                $fitTypes = [FitType::SLIM->value, FitType::REGULAR->value, FitType::LOOSE->value, FitType::RELAXED->value];
                return $fitTypes[array_rand($fitTypes)];
            }
        }

        return null;
    }

    /**
     * Get pattern
     */
    private function getPattern(): string
    {
        $patterns = Pattern::values();
        return $patterns[array_rand($patterns)];
    }

    /**
     * Get occasion
     */
    private function getOccasion(string $category, string $parent): string
    {
        if (str_contains($category, 'formal') || str_contains($parent, 'formal')) {
            return rand(0, 1) ? Occasion::FORMAL->value : Occasion::OFFICE->value;
        } elseif (str_contains($category, 'party') || str_contains($category, 'designer')) {
            return Occasion::PARTY->value;
        } elseif (str_contains($category, 'wedding') || str_contains($category, 'festive') || str_contains($category, 'silk') || str_contains($category, 'embroidered')) {
            return rand(0, 1) ? Occasion::WEDDING->value : Occasion::FESTIVE->value;
        } elseif (str_contains($category, 'sports') || str_contains($category, 'joggers') || str_contains($category, 'athletic')) {
            return Occasion::SPORTS->value;
        } else {
            return rand(0, 1) ? Occasion::CASUAL->value : Occasion::EVERYDAY->value;
        }
    }

    /**
     * Get short description
     */
    private function getShortDescription(string $category, string $brand, string $material): string
    {
        $descriptions = [
            "Premium quality {$material} {$category} from {$brand}. Comfortable fit and stylish design.",
            "High-quality {$category} made with {$material}. Perfect for everyday wear.",
            "Stylish {$category} crafted from premium {$material}. Ideal for all occasions.",
            "Comfortable and durable {$category} in {$material}. Great value for money.",
            "{$brand} presents this premium {$category} in finest {$material}.",
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get detailed description
     */
    private function getDescription(string $name, string $material, string $brand): string
    {
        return "Introducing the {$name} from {$brand}, a perfect blend of style and comfort. " .
            "Crafted from premium {$material}, this product ensures durability and a comfortable fit throughout the day. " .
            "The modern design makes it suitable for various occasions, whether you're heading to the office, meeting friends, or enjoying a casual day out. " .
            "\n\nKey Features:\n" .
            "- Premium {$material} fabric\n" .
            "- Comfortable and breathable\n" .
            "- Easy to care and maintain\n" .
            "- Perfect fit for all body types\n" .
            "- Color-fast and long-lasting\n" .
            "\n\nCare Instructions:\n" .
            "Machine wash cold with like colors. Do not bleach. Tumble dry low. Warm iron if needed.";
    }

    /**
     * Get tags for search
     */
    private function getTags(string $category, string $parent, string $brand): array
    {
        $tags = [$category, $brand];

        if ($parent) {
            $tags[] = $parent;
        }

        $commonTags = ['fashion', 'clothing', 'online shopping', 'bangladesh'];

        return array_merge($tags, array_slice($commonTags, 0, 2));
    }

    /**
     * Create product variants with sizes and colors
     */
    private function createProductVariants(Product $product, string $category, string $grandParent): void
    {
        // Get appropriate sizes based on category
        $sizes = $this->getSizesForCategory($category, $grandParent);
        $colors = $this->getColorsForProduct();

        // Ensure we have at least some sizes and colors
        if ($sizes->isEmpty() || $colors->isEmpty()) {
            return;
        }

        // Create variants for each size and color combination
        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id' => $size->id,
                    'color_id' => $color->id,
                    'sku' => $product->sku . '-S' . $size->id . 'C' . $color->id,
                    'price' => null, // Use product base price
                    'stock_in' => rand(10, 50),
                    'stock_out' => rand(0, 5),
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Get sizes appropriate for category
     */
    private function getSizesForCategory(string $category, string $grandParent): \Illuminate\Support\Collection
    {
        // Determine size type based on category
        if (str_contains($category, 'kids') || str_contains($grandParent, 'kids')) {
            // Kids sizes
            return Size::whereIn('code', ['2y', '4y', '6y', '8y', '10y', '12y'])->get();
        } elseif (str_contains($category, 'baby')) {
            // Baby sizes
            return Size::whereIn('code', ['0-6m', '6-12m', '12-18m', '18-24m'])->get();
        } elseif (str_contains($category, 'shoe') || str_contains($category, 'footwear') || str_contains($category, 'sneaker') || str_contains($category, 'sandal')) {
            // Shoe sizes
            return Size::whereIn('code', ['39', '40-shoe', '41', '42-shoe', '43', '44'])->get();
        } else {
            // Standard clothing sizes
            return Size::whereIn('code', ['s', 'm', 'l', 'xl', 'xxl'])->get();
        }
    }

    /**
     * Get random colors for product (3-5 colors)
     */
    private function getColorsForProduct(): \Illuminate\Support\Collection
    {
        $colorCount = rand(3, 5);
        return Color::inRandomOrder()->take($colorCount)->get();
    }
}
