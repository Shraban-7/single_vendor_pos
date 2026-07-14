<?php

namespace App\Models;

use App\Enums\PaymentMethodType;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'payment_method' => PaymentMethodType::class,
        'payment_type' => PaymentType::class,
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function generatePaymentNumber()
    {
        $lastPayment = Payment::latest()->first();
        $lastNumber = $lastPayment ? (int) str_replace('PAY-', '', $lastPayment->payment_number) : 0;

        return 'PAY-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}
