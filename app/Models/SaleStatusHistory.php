<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'sale_status_histories';

    protected $fillable = [
        'sale_id',
        'status',
        'comment',
        'updated_by',
    ];

    protected $casts = [
        'status' => SaleStatus::class,
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getStatusIconAttribute(): string
    {
        return $this->status->icon();
    }
}
