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

    public function exchangeItems()
    {
        return $this->hasMany(ExchangeItem::class, 'sale_return_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getReturnNumberAttribute()
    {
        return $this->returned_id;
    }

    public function getExchangeValueAttribute()
    {
        return $this->exchangeItems->sum('subtotal');
    }

}
