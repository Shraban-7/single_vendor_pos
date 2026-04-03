<?php

namespace App\Enums;

enum DeliveryZone: string
{
    case INSIDE_DHAKA = 'inside_dhaka';
    case OUTSIDE_DHAKA = 'outside_dhaka';

    public function label(): string
    {
        return match ($this) {
            self::INSIDE_DHAKA => 'Inside Dhaka',
            self::OUTSIDE_DHAKA => 'Outside Dhaka',
        };
    }

    public function shippingCost(): int
    {
        return match ($this) {
            self::INSIDE_DHAKA => 60,
            self::OUTSIDE_DHAKA => 120,
        };
    }

    public function estimatedDays(): string
    {
        return match ($this) {
            self::INSIDE_DHAKA => '1-2 business days',
            self::OUTSIDE_DHAKA => '3-5 business days',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
