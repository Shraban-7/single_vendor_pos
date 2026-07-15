<?php

namespace App\Enums;

enum BillingCycle: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::MONTHLY => 'মাসিক',
            self::YEARLY => 'বাৎসরিক',
        };
    }
}
