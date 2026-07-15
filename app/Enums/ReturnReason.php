<?php

namespace App\Enums;

enum ReturnReason: string
{
    case DEFECTIVE = 'defective';
    case WRONG_ITEM = 'wrong_item';
    case NOT_NEEDED = 'not_needed';
    case DAMAGED = 'damaged';
    case EXPIRED = 'expired';
    case QUALITY_ISSUE = 'quality_issue';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::DEFECTIVE => 'Defective',
            self::WRONG_ITEM => 'Wrong Item',
            self::NOT_NEEDED => 'Not Needed',
            self::DAMAGED => 'Damaged',
            self::EXPIRED => 'Expired',
            self::QUALITY_ISSUE => 'Quality Issue',
            self::OTHER => 'Other',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::DEFECTIVE => 'ত্রুটিপূর্ণ',
            self::WRONG_ITEM => 'ভুল পণ্য',
            self::NOT_NEEDED => 'প্রয়োজন নেই',
            self::DAMAGED => 'ক্ষতিগ্রস্ত',
            self::EXPIRED => 'মেয়াদোত্তীর্ণ',
            self::QUALITY_ISSUE => 'মানসম্মত নয়',
            self::OTHER => 'অন্যান্য',
        };
    }
}
