<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case ORDERED = 'ordered';
    case RECEIVED = 'received';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ORDERED => 'Ordered',
            self::RECEIVED => 'Received',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::DRAFT => 'খসড়া',
            self::ORDERED => 'অর্ডারকৃত',
            self::RECEIVED => 'গৃহীত',
            self::COMPLETED => 'সম্পন্ন',
            self::CANCELLED => 'বাতিল',
        };
    }
}
