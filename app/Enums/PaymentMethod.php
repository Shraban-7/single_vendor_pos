<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case COD = 'cod';
    case BKASH = 'bkash';
    case NAGAD = 'nagad';
    case CARD = 'card';
    case BANK = 'bank';
    case ONLINE = 'online';
    case CASH = 'cash';

    case NONE = 'none';

    /**
     * Human readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::COD => 'Cash on Delivery',
            self::BKASH => 'bKash',
            self::NAGAD => 'Nagad',
            self::CARD => 'Credit/Debit Card',
            self::BANK => 'Bank Transfer',
            self::ONLINE => 'Online Payment',
            self::CASH => 'Cash Payment',
            self::NONE => 'None',

        };
    }

    /**
     * UI icon class
     */
    public function icon(): string
    {
        return match ($this) {
            self::COD => 'fa-money-bill-wave',
            self::BKASH => 'fa-mobile-alt',
            self::NAGAD => 'fa-mobile-alt',
            self::CARD => 'fa-credit-card',
            self::BANK => 'fa-university',
            self::ONLINE => 'fa-globe',
            self::CASH => 'fa-money-bill',
        };
    }

    /**
     * Payments that require transaction reference
     */
    public function requiresTransactionId(): bool
    {
        return in_array($this, [
            self::BKASH,
            self::NAGAD,
            self::BANK,
            self::ONLINE,
        ]);
    }

    /**
     * Business logic: COD is not prepaid
     */
    public function isPrepaid(): bool
    {
        return $this !== self::COD;
    }

    /**
     * Online payment check
     */
    public function isOnline(): bool
    {
        return $this === self::ONLINE;
    }

    /**
     * Get all enum values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Mobile wallets only
     */
    public static function mobileWallets(): array
    {
        return [
            self::BKASH,
            self::NAGAD,
        ];
    }
}