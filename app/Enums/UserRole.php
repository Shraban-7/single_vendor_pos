<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case STAFF = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Manager',
            self::STAFF => 'Staff',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER]);
    }

    public function canAccessDashboard(): bool
    {
        return $this !== self::CUSTOMER;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function staffRoles(): array
    {
        return [self::ADMIN, self::MANAGER, self::STAFF];
    }
}
