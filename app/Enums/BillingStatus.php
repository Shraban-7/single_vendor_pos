<?php

namespace App\Enums;

enum BillingStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::PENDING => 'পেন্ডিং',
            self::PAID => 'পরিশোধিত',
            self::FAILED => 'ব্যর্থ',
            self::REFUNDED => 'রিফান্ডকৃত',
            self::CANCELLED => 'বাতিল',
        };
    }
}
