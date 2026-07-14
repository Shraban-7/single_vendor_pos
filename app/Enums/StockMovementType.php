<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';
    case DAMAGE = 'damage';
    case TRANSFER = 'transfer';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PURCHASE => 'Purchase',
            self::SALE => 'Sale',
            self::ADJUSTMENT => 'Adjustment',
            self::RETURN => 'Return',
            self::DAMAGE => 'Damage',
            self::TRANSFER => 'Transfer',
            self::EXPIRED => 'Expired',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::PURCHASE => 'ক্রয়',
            self::SALE => 'বিক্রয়',
            self::ADJUSTMENT => 'সমন্বয়',
            self::RETURN => 'ফেরত',
            self::DAMAGE => 'ক্ষতিগ্রস্ত',
            self::TRANSFER => 'স্থানান্তর',
            self::EXPIRED => 'মেয়াদোত্তীর্ণ',
        };
    }

    public function color_code(): string
    {
        return match ($this) {
            self::PURCHASE => '#3b82f6',
            self::SALE => '#22c55e',
            self::ADJUSTMENT => '#eab308',
            self::RETURN => '#ef4444',
            self::DAMAGE => '#ef4444',
            self::TRANSFER => '#3b82f6',
            self::EXPIRED => '#302d2dff',
        };
    }
}
