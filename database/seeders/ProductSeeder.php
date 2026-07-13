<?php

namespace Database\Seeders;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private $materials = [
        't-shirt' => ['100% Cotton', 'Cotton Blend', 'Premium Combed Cotton'],
        'shirt' => ['Cotton', 'Cotton Twill', 'Denim', 'Linen', 'Oxford Cotton'],
        'pant' => ['Denim', 'Cotton Twill', 'Chino Cotton', 'Gabardine'],
        'panjabi' => ['Cotton', 'Premium Cotton', 'Silk', 'Semi Silk', 'Khadi', 'Jamdani', 'Linen'],
        'saree' => ['Cotton', 'Silk', 'Georgette', 'Jamdani', 'Katan', 'Banarasi', 'Tangail Cotton', 'Rajshahi Silk'],
        'salwar' => ['Cotton', 'Lawn Cotton', 'Georgette', 'Silk Blend', 'Khadi'],
        'kurti' => ['Cotton', 'Rayon', 'Georgette', 'Linen'],
        'winter' => ['Fleece', 'Wool', 'Cotton Blend', 'Cashmere Blend', 'Leather'],
        'bag' => ['Genuine Leather', 'PU Leather', 'Canvas', 'Nylon'],
        'summer' => ['Pure Cotton', 'Linen', 'Cotton Blend', 'Rayon', 'Georgette', 'Viscose'],
    ];

    public function run(): void
    {
        Product::query()->delete();

        // Get all leaf categories (categories without children)
        $leafCategories = Category::doesntHave('children')->get();

        foreach ($leafCategories as $category) {
            $this->createProductsForCategory($category, 4); // 4 products per leaf category
        }
    }

    private function createProductsForCategory(Category $category, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $productData = $this->generateProductData($category, $i);
            unset($productData['_full_path']);
            $product = Product::create($productData);
        }
    }

    private function generateProductData(Category $category, int $index): array
    {
        // Walk the ancestor chain so category_id / subcategory_id / sub_subcategory_id
        // are filled correctly no matter how deep this leaf category sits.
        $chain = [$category];
        $cursor = $category;
        while ($cursor->parent) {
            $cursor = $cursor->parent;
            $chain[] = $cursor;
        }
        $chain = array_reverse($chain); // [top, mid, ..., leaf]

        $categoryId = $chain[0]->id ?? null;
        $subcategoryId = $chain[1]->id ?? null;
        $subSubcategoryId = $chain[2]->id ?? null;

        $categoryName = strtolower($category->name);
        $parentName = $category->parent ? strtolower($category->parent->name) : '';
        $grandParentName = $category->parent && $category->parent->parent ? strtolower($category->parent->parent->name) : '';

        $fullPath = $categoryName . ' ' . $parentName . ' ' . $grandParentName;

        $material = $this->getMaterial($fullPath);

        // Bangladeshi Standard Product Name
        $productPrefixes = ['Premium', 'Exclusive', 'Classic', 'Designer', 'Authentic', 'Trendy', 'Stylish', 'Traditional'];
        $prefix = $productPrefixes[array_rand($productPrefixes)];
        $productName = $prefix . ' ' . $category->name;

        $pricing = $this->getPricing($fullPath);

        $isOnSale = rand(0, 100) < 30; // 30% chance of being on sale
        $isFeatured = rand(0, 100) < 20; // 20% chance of being featured
        $isNewArrival = rand(0, 100) < 25; // 25% chance of being new arrival
        $isBestSeller = rand(0, 100) < 15;

        $slugBase = Str::slug($productName . '-' . uniqid());

        return [
            'name' => $productName,
            'slug' => $slugBase,
            'sku' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', 'bd'), 0, 3)) . '-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $categoryName), 0, 3)) . rand(1000, 9999),
            'image' => 'demo-products/' . Str::slug($category->name) . '.png',
            'short_description' => "High-quality {$category->name} in Bangladesh. {$material} fabric, comfortable and stylish.",
            'description' => "Get the best {$category->name} in Bangladesh. Made with premium {$material}. Perfect for fashion-conscious individuals. We ensure the best quality and standard sizing for the Bangladeshi market.",
            'price' => $pricing['price'],
            'compare_price' => $isOnSale ? $pricing['compare_price'] : null,
            'cost_price' => $pricing['cost_price'],
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'sub_subcategory_id' => $subSubcategoryId,
            'material' => $material,
            'fit_type' => $this->getFitType($fullPath),
            'pattern' => $this->getPattern(),
            'occasion' => $this->getOccasion($fullPath),
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
            'meta_title' => $productName . ' - Buy Online in BD',
            'meta_description' => "Shop {$productName} in Bangladesh. Best price guaranteed.",
            'tags' => json_encode([$category->name, 'Bangladesh', 'Fashion', 'Online Shopping BD']),
            // internal-only key, stripped before Product::create()
            '_full_path' => $fullPath,
        ];
    }

    private function getMaterial(string $fullPath): string
    {
        if (str_contains($fullPath, 't-shirt') || str_contains($fullPath, 'tshirt')) return $this->getRandom($this->materials['t-shirt']);
        if (str_contains($fullPath, 'shirt')) return $this->getRandom($this->materials['shirt']);
        if (str_contains($fullPath, 'pant') || str_contains($fullPath, 'jeans')) return $this->getRandom($this->materials['pant']);
        if (str_contains($fullPath, 'panjabi')) return $this->getRandom($this->materials['panjabi']);
        if (str_contains($fullPath, 'saree')) return $this->getRandom($this->materials['saree']);
        if (str_contains($fullPath, 'salwar')) return $this->getRandom($this->materials['salwar']);
        if (str_contains($fullPath, 'kurti')) return $this->getRandom($this->materials['kurti']);
        if (str_contains($fullPath, 'winter') || str_contains($fullPath, 'hoodie') || str_contains($fullPath, 'jacket')) return $this->getRandom($this->materials['winter']);
        if (str_contains($fullPath, 'bag') || str_contains($fullPath, 'wallet')) return $this->getRandom($this->materials['bag']);
        if (str_contains($fullPath, 'summer') || str_contains($fullPath, 'sun') || str_contains($fullPath, 'sunglass')) return $this->getRandom($this->materials['summer']);

        return 'Standard Material';
    }

    private function getRandom(array $array)
    {
        return $array[array_rand($array)];
    }

    private function getPricing(string $fullPath): array
    {
        // General Bangladeshi Pricing in BDT
        $min = 500;
        $max = 3000;

        if (str_contains($fullPath, 't-shirt') || str_contains($fullPath, 'tshirt')) { $min = 300; $max = 1200; }
        elseif (str_contains($fullPath, 'shirt')) { $min = 800; $max = 3500; }
        elseif (str_contains($fullPath, 'pant') || str_contains($fullPath, 'jeans')) { $min = 1000; $max = 4000; }
        elseif (str_contains($fullPath, 'panjabi')) { $min = 1200; $max = 8000; }
        elseif (str_contains($fullPath, 'saree')) { $min = 1500; $max = 25000; }
        elseif (str_contains($fullPath, 'salwar')) { $min = 1200; $max = 8000; }
        elseif (str_contains($fullPath, 'kurti')) { $min = 800; $max = 3500; }
        elseif (str_contains($fullPath, 'winter') || str_contains($fullPath, 'jacket')) { $min = 1500; $max = 6000; }
        elseif (str_contains($fullPath, 'shoe') || str_contains($fullPath, 'sneaker')) { $min = 1500; $max = 8000; }
        elseif (str_contains($fullPath, 'sandal')) { $min = 500; $max = 2500; }
        elseif (str_contains($fullPath, 'bag') || str_contains($fullPath, 'backpack')) { $min = 1000; $max = 5000; }
        elseif (str_contains($fullPath, 'wallet') || str_contains($fullPath, 'belt')) { $min = 400; $max = 2000; }
        elseif (str_contains($fullPath, 'kid')) { $min = 300; $max = 2000; }
        elseif (str_contains($fullPath, 'summer') || str_contains($fullPath, 'sun') || str_contains($fullPath, 'sunglass')) { $min = 400; $max = 2500; }
        elseif (str_contains($fullPath, 'islamic') || str_contains($fullPath, 'jubba') || str_contains($fullPath, 'abaya')) { $min = 1500; $max = 6000; }

        $price = rand($min, $max);
        // Round to nearest 50
        $price = round($price / 50) * 50;

        $costPrice = $price * rand(50, 70) / 100; // 30-50% margin
        // Round cost price to nearest 50
        $costPrice = round($costPrice / 50) * 50;

        $comparePrice = $price * rand(115, 150) / 100; // 15-50% "was" price
        // Round compare price to nearest 50
        $comparePrice = round($comparePrice / 50) * 50;

        return [
            'price' => $price,
            'cost_price' => $costPrice,
            'compare_price' => $comparePrice,
        ];
    }

    private function getFitType(string $fullPath): ?string
    {
        $wearCategories = ['shirt', 't-shirt', 'tshirt', 'panjabi', 'pant', 'jeans', 'kurti', 'salwar', 'short', 'dress', 'top', 'vest', 'frock', 'jogger', 'palazzo'];
        foreach ($wearCategories as $wear) {
            if (str_contains($fullPath, $wear)) {
                $fitTypes = [FitType::SLIM->value, FitType::REGULAR->value, FitType::LOOSE->value, FitType::RELAXED->value];
                return $fitTypes[array_rand($fitTypes)];
            }
        }
        return null;
    }

    private function getPattern(): ?string
    {
        $patterns = Pattern::values();
        if (empty($patterns)) return null;
        return $patterns[array_rand($patterns)];
    }

    private function getOccasion(string $fullPath): ?string
    {
        $values = Occasion::values();
        if (empty($values)) return null;

        if (str_contains($fullPath, 'formal') && in_array(Occasion::FORMAL->value, $values)) return Occasion::FORMAL->value;
        if ((str_contains($fullPath, 'party') || str_contains($fullPath, 'saree') || str_contains($fullPath, 'silk')) && in_array(Occasion::PARTY->value, $values)) return Occasion::PARTY->value;
        if ((str_contains($fullPath, 'wedding') || str_contains($fullPath, 'katan') || str_contains($fullPath, 'banarasi')) && in_array(Occasion::WEDDING->value, $values)) return Occasion::WEDDING->value;
        if ((str_contains($fullPath, 'sports') || str_contains($fullPath, 'jogger') || str_contains($fullPath, 'sneaker')) && in_array(Occasion::SPORTS->value, $values)) return Occasion::SPORTS->value;

        $fallback = in_array(Occasion::EVERYDAY->value ?? null, $values) ? Occasion::EVERYDAY->value : $values[0];
        $casual = in_array(Occasion::CASUAL->value ?? null, $values) ? Occasion::CASUAL->value : $fallback;

        return rand(0, 1) ? $casual : $fallback;
    }


}
