<?php

namespace App\Enums;

enum Occasion: string
{
    case CASUAL = 'casual';
    case FORMAL = 'formal';
    case PARTY = 'party';
    case WEDDING = 'wedding';
    case FESTIVE = 'festive';
    case OFFICE = 'office';
    case SPORTS = 'sports';
    case EVERYDAY = 'everyday';

    public function label(): string
    {
        return match ($this) {
            self::CASUAL => 'Casual',
            self::FORMAL => 'Formal',
            self::PARTY => 'Party Wear',
            self::WEDDING => 'Wedding',
            self::FESTIVE => 'Festive',
            self::OFFICE => 'Office Wear',
            self::SPORTS => 'Sports',
            self::EVERYDAY => 'Everyday',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
