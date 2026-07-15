<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => \App\Enums\StockMovementType::class,
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'before_quantity' => 'decimal:2',
        'after_quantity' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
