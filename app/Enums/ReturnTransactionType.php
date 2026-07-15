<?php

namespace App\Enums;

enum ReturnTransactionType: string
{
    case RETURN = 'return';
    case EXCHANGE = 'exchange';

    public function label(): string
    {
        return match ($this) {
            self::RETURN => 'Return',
            self::EXCHANGE => 'Exchange',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::RETURN => 'ফেরত',
            self::EXCHANGE => 'বিনিময়',
        };
    }
}
