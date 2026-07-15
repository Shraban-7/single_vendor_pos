<?php

namespace App\Enums;

enum BackupType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';
    case SCHEDULED = 'scheduled';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::AUTOMATIC => 'Automatic',
            self::SCHEDULED => 'Scheduled',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::MANUAL => 'ম্যানুয়াল',
            self::AUTOMATIC => 'স্বয়ংক্রিয়',
            self::SCHEDULED => 'তফসিলি',
        };
    }
}
