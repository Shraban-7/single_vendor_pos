<?php

namespace App\Models;

use App\Enums\FitType;
use App\Enums\Occasion;
use App\Enums\Pattern;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'name',
    //     'slug',
    //     'sku',
    //     'image',
    //     'short_description',
    //     'description',
    //     'price',
    //     'compare_price',
    //     'cost_price',
    //     'category_id',
    //     'subcategory_id',
    //     'brand',
    //     'material',
    //     'fit_type',
    //     'pattern',
    //     'occasion',
    //     'stock_in',
    //     'low_stock_threshold',
    //     'weight',
    //     'is_active',
    //     'is_featured',
    //     'is_new_arrival',
    //     'is_best_seller',
    //     'is_on_sale',
    //     'average_rating',
    //     'review_count',
    //     'view_count',
    //     'sold_count',
    //     'meta_title',
    //     'meta_description',
    //     'tags',
    // ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'stock_in' => 'integer',
        'low_stock_threshold' => 'integer',
        'review_count' => 'integer',
        'view_count' => 'integer',
        'sold_count' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_on_sale' => 'boolean',
        'tags' => 'array',
        'fit_type' => FitType::class,
        'pattern' => Pattern::class,
        'occasion' => Occasion::class,
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_variants')
            ->withPivot('color_id', 'stock_in', 'sku')
            ->distinct();
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_variants')
            ->withPivot('size_id', 'stock_in', 'sku')
            ->distinct();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeBestSellers($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_in', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_in', '<=', 'low_stock_threshold')
            ->where('stock_in', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_in', '<=', 0);
    }

    // Helpers
    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->images()->where('is_primary', true)->first()?->image_url
            ?? $this->images()->first()?->image_url;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function isLowStock(): bool
    {
        return $this->stock_in > 0 && $this->stock_in <= $this->low_stock_threshold;
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function updateRating(): void
    {
        $this->update([
            'average_rating' => $this->approvedReviews()->avg('rating') ?? 0,
            'review_count' => $this->approvedReviews()->count(),
        ]);
    }

    public function thumbnail(): Attribute
    {
        //$default = "assets/images/products/default" . rand(1, 5) . '.webp';
        $default = "assets/images/default.png";

        return Attribute::make(
            get: fn() => $this->image ? storage_url($this->image) : asset($default),
            // get: fn() => $this->image ? storage_url($this->image) : asset('assets/images/default.png'),
        );
    }

    public function currentStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->stock_in - $this->stock_out;
            }
        );
    }

    public function totalStock(): Attribute
    {
        $stock = $this->stock_in - $this->stock_out;
        
        foreach ($this->variants as $variant) {
            $stock += $variant->stock_in - $variant->stock_out;
        }

        return Attribute::make(
            get: function () use ($stock) {
                return $stock;
            }
        );
    }

    public static function generate_sku($length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $sku = 'P';
            for ($i = 0; $i < $length; $i++) {
                $sku .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
