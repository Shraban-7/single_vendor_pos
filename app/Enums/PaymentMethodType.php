<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case BANK = 'bank';
    case MOBILE_BANKING = 'mobile_banking';
    case OTHER = 'other';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CARD => 'Card',
            self::BANK => 'Bank',
            self::MOBILE_BANKING => 'Mobile Banking',
            self::OTHER => 'Other',
            self::NONE => 'None',
        };
    }
}
