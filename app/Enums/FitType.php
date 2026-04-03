<?php

namespace App\Enums;

enum FitType: string
{
    case SLIM = 'slim';
    case REGULAR = 'regular';
    case LOOSE = 'loose';
    case RELAXED = 'relaxed';
    case TAILORED = 'tailored';

    public function label(): string
    {
        return match ($this) {
            self::SLIM => 'Slim Fit',
            self::REGULAR => 'Regular Fit',
            self::LOOSE => 'Loose Fit',
            self::RELAXED => 'Relaxed Fit',
            self::TAILORED => 'Tailored Fit',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
