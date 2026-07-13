<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'type',
        'settings',
        'starts_at',
        'ends_at',
        'sort_order',
        'is_active',
        'show_countdown',
        'view_all_url',
        'source'
    ];

    protected $casts = [
        'type' => \App\Enums\HomeSectionType::class,
        'source' => \App\Enums\HomeSectionSource::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'show_countdown' => 'boolean',
        'settings' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(HomeSectionItem::class, 'home_section_id')->orderBy('sort_order', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }
}
