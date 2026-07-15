<?php

namespace App\Enums;

enum BackupStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::PENDING => 'পেন্ডিং',
            self::COMPLETED => 'সম্পন্ন',
            self::FAILED => 'ব্যর্থ',
        };
    }
}
