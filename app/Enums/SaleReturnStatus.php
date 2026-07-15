<?php

namespace App\Enums;

enum SaleReturnStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::PENDING => 'পেন্ডিং',
            self::APPROVED => 'অনুমোদিত',
            self::REJECTED => 'প্রত্যাখ্যাত',
            self::COMPLETED => 'সম্পন্ন',
            self::CANCELLED => 'বাতিল',
        };
    }
}
