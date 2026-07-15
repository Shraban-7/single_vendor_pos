<?php

namespace App\Enums;

enum SaleStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::RETURNED => 'Returned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::SHIPPED => 'indigo',
            self::DELIVERED, self::COMPLETED => 'green',
            self::CANCELLED, self::RETURNED => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'fa-file',
            self::PENDING => 'fa-clock',
            self::CONFIRMED => 'fa-check-circle',
            self::SHIPPED => 'fa-truck',
            self::DELIVERED => 'fa-box-open',
            self::COMPLETED => 'fa-check-double',
            self::CANCELLED => 'fa-times-circle',
            self::RETURNED => 'fa-undo',
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this, [self::PENDING, self::CONFIRMED]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::DELIVERED, self::COMPLETED]);
    }

    public static function activeStatuses(): array
    {
        return [self::PENDING, self::CONFIRMED, self::SHIPPED];
    }
}
