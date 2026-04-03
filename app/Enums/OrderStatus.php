<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVERY => 'Out for Delivery',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::RETURNED => 'Returned',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::PROCESSING => 'indigo',
            self::SHIPPED => 'purple',
            self::OUT_FOR_DELIVERY => 'orange',
            self::DELIVERED => 'green',
            self::CANCELLED => 'red',
            self::RETURNED => 'gray',
            self::REFUNDED => 'pink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'fa-clock',
            self::CONFIRMED => 'fa-check-circle',
            self::PROCESSING => 'fa-cog',
            self::SHIPPED => 'fa-shipping-fast',
            self::OUT_FOR_DELIVERY => 'fa-truck',
            self::DELIVERED => 'fa-check-double',
            self::CANCELLED => 'fa-times-circle',
            self::RETURNED => 'fa-undo',
            self::REFUNDED => 'fa-money-bill-wave',
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
        ]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [
            self::DELIVERED,
            self::CANCELLED,
            self::RETURNED,
            self::REFUNDED,
        ]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function activeStatuses(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
            self::SHIPPED,
            self::OUT_FOR_DELIVERY,
        ];
    }
}
