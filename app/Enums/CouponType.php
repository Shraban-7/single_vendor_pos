<?php

namespace App\Enums;

enum CouponType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage Discount',
            self::FIXED => 'Fixed Amount',
        };
    }

    public function formatDiscount(float $value): string
    {
        return match ($this) {
            self::PERCENTAGE => $value . '%',
            self::FIXED => '৳' . number_format($value),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
