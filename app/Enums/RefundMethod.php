<?php

namespace App\Enums;

enum RefundMethod: string
{
    case CASH = 'cash';
    case ACCOUNT_CREDIT = 'account_credit';
    case BANK = 'bank';
    case MOBILE_BANKING = 'mobile_banking';
    case ORIGINAL_PAYMENT = 'original_payment';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::ACCOUNT_CREDIT => 'Account Credit',
            self::BANK => 'Bank',
            self::MOBILE_BANKING => 'Mobile Banking',
            self::ORIGINAL_PAYMENT => 'Original Payment',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::CASH => 'নগদ',
            self::ACCOUNT_CREDIT => 'অ্যাকাউন্ট ক্রেডিট',
            self::BANK => 'ব্যাংক',
            self::MOBILE_BANKING => 'মোবাইল ব্যাংকিং',
            self::ORIGINAL_PAYMENT => 'মূল পেমেন্ট পদ্ধতি',
        };
    }
}
