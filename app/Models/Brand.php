<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOwn($query)
    {
        return $query->where('own_brand', true);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
