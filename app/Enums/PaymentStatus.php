<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case UNPAID = 'unpaid'; // ✅ NEW
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::UNPAID => 'Unpaid', // ✅ NEW
            self::PAID => 'Paid',
            self::PARTIAL => 'Partial Paid',
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
            self::UNPAID => 'red', // ✅ NEW (strong warning)
            self::PAID => 'green',
            self::PARTIAL => 'purple', // or 'orange'
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
            self::UNPAID => 'fa-exclamation-circle', // ✅ NEW
            self::PAID => 'fa-check-circle',
            self::PARTIAL => 'fa-hourglass-half',
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

    public function isPartial(): bool
    {
        return $this === self::PARTIAL;
    }

    public function isUnpaid(): bool // ✅ helper
    {
        return $this === self::UNPAID;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}