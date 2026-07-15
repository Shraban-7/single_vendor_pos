<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id');
    }

}
