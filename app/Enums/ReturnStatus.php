<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case NONE = 'none';
    case PARTIAL = 'partial';
    case FULL = 'full';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'None',
            self::PARTIAL => 'Partial',
            self::FULL => 'Full',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::NONE => 'কোনোটিই নয়',
            self::PARTIAL => 'আংশিক',
            self::FULL => 'সম্পূর্ণ',
        };
    }
}
