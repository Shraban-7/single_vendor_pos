<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int                    $id
 * @property string                 $name
 * @property string                 $slug
 * @property array<string, mixed>   $credentials
 * @property bool                   $is_active
 */
class CourierProvider extends Model
{
    protected $table = 'courier_providers';

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'credentials',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'credentials' => 'array',
        'is_active'   => 'boolean',
    ];
}
