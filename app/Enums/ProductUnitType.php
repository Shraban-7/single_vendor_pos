<?php

namespace App\Enums;

enum ProductUnitType: string
{
    case WEIGHT = 'weight';
    case VOLUME = 'volume';
    case LENGTH = 'length';
    case QUANTITY = 'quantity';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WEIGHT => 'Weight',
            self::VOLUME => 'Volume',
            self::LENGTH => 'Length',
            self::QUANTITY => 'Quantity',
            self::OTHER => 'Other',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::WEIGHT => 'ওজন',
            self::VOLUME => 'আয়তন',
            self::LENGTH => 'দৈর্ঘ্য',
            self::QUANTITY => 'পরিমাণ',
            self::OTHER => 'অন্যান্য',
        };
    }
}
