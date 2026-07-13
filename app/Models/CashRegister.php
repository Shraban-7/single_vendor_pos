<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'opened_at' => 'date',
        'closed_at' => 'date'
    ];

    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }
}
