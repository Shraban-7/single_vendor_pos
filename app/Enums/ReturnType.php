<?php

namespace App\Enums;

enum ReturnType: string
{
    case FULL = 'full';
    case PARTIAL = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::FULL => 'Full',
            self::PARTIAL => 'Partial',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::FULL => 'সম্পূর্ণ',
            self::PARTIAL => 'আংশিক',
        };
    }
}
