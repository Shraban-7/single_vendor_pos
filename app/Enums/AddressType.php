<?php

namespace App\Enums;

enum AddressType: string
{
    case SHIPPING = 'shipping';
    case BILLING = 'billing';

    public function label(): string
    {
        return match ($this) {
            self::SHIPPING => 'Shipping Address',
            self::BILLING => 'Billing Address',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
