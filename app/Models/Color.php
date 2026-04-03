<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'hex_code',
    ];

    // Relationships
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variants')
            ->withPivot('size_id', 'stock_in', 'sku')
            ->distinct();
    }
}
