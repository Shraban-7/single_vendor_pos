<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address_type',
        'district',
        'city',
        'address',
        'postal_code',
        'delivery_zone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'address_type' => AddressType::class,
        'delivery_zone' => DeliveryZone::class,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeShipping($query)
    {
        return $query->where('address_type', AddressType::SHIPPING);
    }

    public function scopeBilling($query)
    {
        return $query->where('address_type', AddressType::BILLING);
    }

    // Helpers
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->district,
            $this->postal_code,
        ]));
    }

    public function getShippingCostAttribute(): int
    {
        return $this->delivery_zone->shippingCost();
    }
}
