<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::DAILY => 'দৈনিক',
            self::WEEKLY => 'সাপ্তাহিক',
            self::MONTHLY => 'মাসিক',
            self::YEARLY => 'বাৎসরিক',
        };
    }
}
