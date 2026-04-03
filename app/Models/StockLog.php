<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function isStockIn(): bool
    {
        return $this->type === 'in';
    }

    public function isStockOut(): bool
    {
        return $this->type === 'out';
    }

    public function getTargetNameAttribute(): string
    {
        if ($this->product_variant_id) {
            return $this->productVariant->product->name . ' (' . $this->productVariant->variant_name . ')';
        }
        return $this->product->name;
    }
}
