<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Real category names pool for fashion e-commerce
     */
    protected static array $categoryNames = [
        'Men',
        'Women',
        'Kids',
        'Accessories',
        'Footwear',
        'T-Shirts',
        'Shirts',
        'Pants',
        'Jeans',
        'Jackets',
        'Dresses',
        'Tops',
        'Skirts',
        'Sarees',
        'Lehenga',
        'Ethnic Wear',
        'Western Wear',
        'Sportswear',
        'Formal Wear',
        'Casual Wear',
        'Party Wear',
        'Winter Collection',
        'Summer Collection',
        'Watches',
        'Bags',
        'Belts',
        'Wallets',
        'Jewelry',
        'Sunglasses',
        'Scarves',
        'Hats',
        'Caps',
        'Sneakers',
        'Sandals',
        'Boots',
        'Heels',
        'Flats',
        'Sports Shoes',
        'Formal Shoes',
        'Casual Shoes',
        'Kurta',
        'Kurti',
        'Salwar Kameez',
        'Palazzo',
        'Dupatta',
        'Sherwani',
        'Panjabi',
        'Dhoti',
        'Lungi',
        'Innerwear',
        'Sleepwear',
        'Loungewear',
        'Activewear',
        'Maternity Wear',
        'Plus Size',
        'Petite',
    ];

    protected static int $nameIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a real category name from the pool
        $name = self::$categoryNames[self::$nameIndex % count(self::$categoryNames)];
        self::$nameIndex++;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
            'icon' => null,
            'image' => null,
            'parent_id' => null,
            'sort_order' => 1,
            'is_active' => true,
            'is_featured' => false,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    /**
     * Indicate that the category is a parent category.
     */
    public function parent(): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => null,
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the category is a child category.
     */
    public function child(?int $parentId = null): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parentId,
            'is_featured' => false,
        ]);
    }

    /**
     * Indicate that the category is featured.
     */
    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific name for the category.
     */
    public function withName(string $name): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

    /**
     * Set a specific icon for the category.
     */
    public function withIcon(string $icon): static
    {
        return $this->state(fn(array $attributes) => [
            'icon' => $icon,
        ]);
    }
}
