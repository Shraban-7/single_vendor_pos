<?php

namespace App\Enums;

enum SystemNotificationType: string
{
    // Sale lifecycle
    case SALE_CREATED = 'sale_created';
    case SALE_CONFIRMED = 'sale_confirmed';
    case SALE_PACKED = 'sale_packed';
    case SALE_SHIPPED = 'sale_shipped';
    case SALE_DELIVERED = 'sale_delivered';
    case SALE_CANCELLED = 'sale_cancelled';

    // Legacy Order lifecycle (aliases)
    case ORDER_CREATED = 'order_created';
    case ORDER_CONFIRMED = 'order_confirmed';
    case ORDER_PACKED = 'order_packed';
    case ORDER_SHIPPED = 'order_shipped';
    case ORDER_DELIVERED = 'order_delivered';
    case ORDER_CANCELLED = 'order_cancelled';

    // Payment
    case PAYMENT_RECEIVED = 'payment_received';
    case PAYMENT_FAILED = 'payment_failed';
    case REFUND_INITIATED = 'refund_initiated';
    case REFUND_COMPLETED = 'refund_completed';

    // Product / catalog
    case PRODUCT_APPROVED = 'product_approved';
    case PRODUCT_REJECTED = 'product_rejected';
    case PRICE_DROP = 'price_drop';
    case STOCK_LOW = 'stock_low';

    // Promotions / marketing
    case COUPON_CREATED = 'coupon_created';
    case FLASH_SALE_STARTED = 'flash_sale_started';
    case CAMPAIGN_LIVE = 'campaign_live';

    // System / account
    case ACCOUNT_CREATED = 'account_created';
    case PASSWORD_CHANGED = 'password_changed';
    case ROLE_CHANGED = 'role_changed';

    // Admin system
    case NEW_MESSAGE = 'new_message';
    case SUPPORT_TICKET = 'support_ticket';
    case SYSTEM_ALERT = 'system_alert';

    public function color(): string
    {
        return match ($this) {
            self::SALE_CREATED,
            self::ORDER_CREATED,
            self::ACCOUNT_CREATED,
            self::NEW_MESSAGE => 'blue',

            self::SALE_CONFIRMED,
            self::ORDER_CONFIRMED,
            self::CAMPAIGN_LIVE,
            self::COUPON_CREATED => 'indigo',

            self::SALE_PACKED,
            self::ORDER_PACKED => 'teal',

            self::SALE_SHIPPED,
            self::ORDER_SHIPPED,
            self::ROLE_CHANGED,
            self::PRICE_DROP => 'purple',

            self::SALE_DELIVERED,
            self::ORDER_DELIVERED,
            self::PAYMENT_RECEIVED,
            self::REFUND_COMPLETED,
            self::PRODUCT_APPROVED => 'green',

            self::SALE_CANCELLED,
            self::ORDER_CANCELLED,
            self::PAYMENT_FAILED,
            self::PRODUCT_REJECTED,
            self::SYSTEM_ALERT => 'red',

            self::REFUND_INITIATED,
            self::STOCK_LOW,
            self::FLASH_SALE_STARTED,
            self::PASSWORD_CHANGED,
            self::SUPPORT_TICKET => 'yellow',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SALE_CREATED => 'fa-shopping-bag',
            self::ORDER_CREATED => 'fa-shopping-bag',
            self::SALE_CONFIRMED => 'fa-check-circle',
            self::ORDER_CONFIRMED => 'fa-check-circle',
            self::SALE_PACKED => 'fa-box',
            self::ORDER_PACKED => 'fa-box',
            self::SALE_SHIPPED => 'fa-truck',
            self::ORDER_SHIPPED => 'fa-truck',
            self::SALE_DELIVERED => 'fa-box-open',
            self::ORDER_DELIVERED => 'fa-box-open',
            self::SALE_CANCELLED => 'fa-times-circle',
            self::ORDER_CANCELLED => 'fa-times-circle',

            self::PAYMENT_RECEIVED => 'fa-credit-card',
            self::PAYMENT_FAILED => 'fa-exclamation-triangle',
            self::REFUND_INITIATED => 'fa-undo',
            self::REFUND_COMPLETED => 'fa-check',

            self::PRODUCT_APPROVED => 'fa-check-circle',
            self::PRODUCT_REJECTED => 'fa-ban',
            self::PRICE_DROP => 'fa-tag',
            self::STOCK_LOW => 'fa-exclamation-circle',

            self::COUPON_CREATED => 'fa-percentage',
            self::FLASH_SALE_STARTED => 'fa-bolt',
            self::CAMPAIGN_LIVE => 'fa-broadcast-tower',

            self::ACCOUNT_CREATED => 'fa-user-plus',
            self::PASSWORD_CHANGED => 'fa-key',
            self::ROLE_CHANGED => 'fa-user-shield',

            self::NEW_MESSAGE => 'fa-envelope',
            self::SUPPORT_TICKET => 'fa-life-ring',
            self::SYSTEM_ALERT => 'fa-exclamation-triangle',
        };
    }
}