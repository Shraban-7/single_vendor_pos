<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PAID => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'orange',
            self::REFUNDED => 'blue',
            self::COMPLETED => 'green',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'fa-clock',
            self::PAID => 'fa-check-circle',
            self::FAILED => 'fa-times-circle',
            self::CANCELLED => 'fa-ban',
            self::REFUNDED => 'fa-undo',
            self::COMPLETED => 'fa-check-double',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
