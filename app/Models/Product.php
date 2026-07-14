<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_returnable' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_in' => 'decimal:2',
        'stock_out' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'stock_alert_quantity' => 'decimal:2',
        'vat_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? storage_url($value) : asset('assets/default_image.png')
        );
    }

    // public function stockMovements()
    // {
    //     return $this->hasMany(StockMovement::class, 'product_id');
    // }

    // public function purchaseItems()
    // {
    //     return $this->hasMany(PurchaseItem::class);
    // }
}
