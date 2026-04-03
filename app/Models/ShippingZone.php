<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'shipping_cost',
        'free_shipping_threshold',
        'estimated_days',
        'is_active',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function calculateShippingCost(float $subtotal): float
    {
        if ($this->free_shipping_threshold && $subtotal >= $this->free_shipping_threshold) {
            return 0;
        }

        return $this->shipping_cost;
    }

    public function hasFreeShipping(float $subtotal): bool
    {
        return $this->free_shipping_threshold && $subtotal >= $this->free_shipping_threshold;
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
