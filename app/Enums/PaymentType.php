<?php

namespace App\Enums;

enum PaymentType: string
{
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case EXPENSE = 'expense';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SALE => 'Sale',
            self::PURCHASE => 'Purchase',
            self::EXPENSE => 'Expense',
            self::OTHER => 'Other',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::SALE => 'বিক্রয়',
            self::PURCHASE => 'ক্রয়',
            self::EXPENSE => 'খরচ',
            self::OTHER => 'অন্যান্য',
        };
    }
}
