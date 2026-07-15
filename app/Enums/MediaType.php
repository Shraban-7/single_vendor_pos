<?php

namespace App\Enums;

enum MediaType: string
{
    case IMAGE = 'image';
    case DOCUMENT = 'document';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Image',
            self::DOCUMENT => 'Document',
            self::VIDEO => 'Video',
            self::AUDIO => 'Audio',
            self::OTHER => 'Other',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::IMAGE => 'ছবি',
            self::DOCUMENT => 'নথি',
            self::VIDEO => 'ভিডিও',
            self::AUDIO => 'অডিও',
            self::OTHER => 'অন্যান্য',
        };
    }
}
