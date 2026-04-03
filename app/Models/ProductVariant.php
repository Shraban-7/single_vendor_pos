<?php

namespace App\Models;

use Dom\Attr;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'stock_in',
        'price',
    ];

    protected $casts = [
        'stock_in' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->price ?? $this->product->price;
    }

    public function getVariantNameAttribute(): string
    {
        $parts = [];
        if ($this->size) {
            $parts[] = $this->size->name;
        }
        if ($this->color) {
            $parts[] = $this->color->name;
        }
        return implode(' / ', $parts);
    }

    public function currentStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->stock_in - $this->stock_out;
            }
        );
    }

    public static function generate_sku($length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $sku = 'V';
            for ($i = 0; $i < $length; $i++) {
                $sku .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }
}
