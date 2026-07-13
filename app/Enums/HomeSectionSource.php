<?php

namespace App\Enums;

enum HomeSectionSource: string
{
    case MANUAL = 'manual';
    case NEW_ARRIVALS = 'new_arrivals';
    case BEST_SELLERS = 'best_sellers';
    case FEATURED = 'featured';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual Selection',
            self::NEW_ARRIVALS => 'New Arrivals',
            self::BEST_SELLERS => 'Best Sellers',
            self::FEATURED => 'Featured Products',
        };
    }
}