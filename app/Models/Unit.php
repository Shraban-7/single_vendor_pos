<?php

namespace App\Models;

use App\Enums\ProductUnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ProductUnitType::class,
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
