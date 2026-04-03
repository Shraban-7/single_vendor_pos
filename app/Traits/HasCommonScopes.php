<?php

namespace App\Traits;

trait HasCommonScopes {

    public function scopeHasBanglaNumbers($query, $column)
    {
        return $query->where($column, 'REGEXP', '[০-৯]');
    }
}