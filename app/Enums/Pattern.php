<?php

namespace App\Enums;

enum Pattern: string
{
    case SOLID = 'solid';
    case PRINTED = 'printed';
    case STRIPED = 'striped';
    case CHECKED = 'checked';
    case FLORAL = 'floral';
    case GEOMETRIC = 'geometric';
    case ABSTRACT = 'abstract';
    case EMBROIDERED = 'embroidered';

    public function label(): string
    {
        return match ($this) {
            self::SOLID => 'Solid',
            self::PRINTED => 'Printed',
            self::STRIPED => 'Striped',
            self::CHECKED => 'Checked',
            self::FLORAL => 'Floral',
            self::GEOMETRIC => 'Geometric',
            self::ABSTRACT => 'Abstract',
            self::EMBROIDERED => 'Embroidered',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
