<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case BANK = 'bank';
    case MOBILE_BANKING = 'mobile_banking';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CARD => 'Card',
            self::BANK => 'Bank',
            self::MOBILE_BANKING => 'Mobile Banking',
            self::OTHER => 'Other',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::CASH => 'নগদ',
            self::CARD => 'কার্ড',
            self::BANK => 'ব্যাংক',
            self::MOBILE_BANKING => 'মোবাইল ব্যাংকিং',
            self::OTHER => 'অন্যান্য',
        };
    }
}
