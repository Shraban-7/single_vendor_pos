<?php

namespace App\Enums;

enum RestockStatus: string
{
    case RESTOCKED = 'restocked';
    case DAMAGED = 'damaged';
    case DISPOSED = 'disposed';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::RESTOCKED => 'Restocked',
            self::DAMAGED => 'Damaged',
            self::DISPOSED => 'Disposed',
            self::PENDING => 'Pending',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::RESTOCKED => 'পুনরায় মজুদকৃত',
            self::DAMAGED => 'ক্ষতিগ্রস্ত',
            self::DISPOSED => 'ফেলে দেওয়া হয়েছে',
            self::PENDING => 'পেন্ডিং',
        };
    }
}
