<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'purchase_count' => 'integer',
    ];

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->image ? storage_url($this->image) : asset('assets/avatar.png');
            },
        );
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
