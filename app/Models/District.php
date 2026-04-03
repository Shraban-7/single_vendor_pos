<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bn_name',
        'shipping_zone_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    // Helpers
    public function getShippingCost(): float
    {
        return $this->shippingZone?->shipping_cost ?? 0;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->bn_name) {
            return "{$this->name} ({$this->bn_name})";
        }
        return $this->name;
    }
}
