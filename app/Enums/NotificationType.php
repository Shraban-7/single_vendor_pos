<?php

namespace App\Enums;

enum NotificationType: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
    case PUSH = 'push';

    public function label(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::EMAIL => 'Email',
            self::PUSH => 'Push Notification',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
