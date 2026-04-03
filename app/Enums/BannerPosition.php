<?php

namespace App\Enums;

enum BannerPosition: string
{
    case HERO = 'hero';
    case PROMOTIONAL = 'promotional';
    case CATEGORY = 'category';
    case FESTIVAL = 'festival';

    public function label(): string
    {
        return match ($this) {
            self::HERO => 'Hero Slider',
            self::PROMOTIONAL => 'Promotional Banner',
            self::CATEGORY => 'Category Banner',
            self::FESTIVAL => 'Festival Banner',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
