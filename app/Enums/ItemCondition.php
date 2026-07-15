<?php

namespace App\Enums;

enum ItemCondition: string
{
    case NEW = 'new';
    case OPENED = 'opened';
    case DAMAGED = 'damaged';
    case DEFECTIVE = 'defective';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::OPENED => 'Opened',
            self::DAMAGED => 'Damaged',
            self::DEFECTIVE => 'Defective',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::NEW => 'নতুন',
            self::OPENED => 'খোলা হয়েছে',
            self::DAMAGED => 'ক্ষতিগ্রস্ত',
            self::DEFECTIVE => 'ত্রুটিপূর্ণ',
        };
    }
}
