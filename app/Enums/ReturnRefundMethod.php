<?php

namespace App\Enums;

enum ReturnRefundMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case MOBILE_BANKING = 'mobile_banking';
    case CARD_REFUND = 'card_refund';
    case ACCOUNT_CREDIT = 'account_credit';
    case CHEQUE = 'cheque';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::MOBILE_BANKING => 'Mobile Banking',
            self::CARD_REFUND => 'Card Refund',
            self::ACCOUNT_CREDIT => 'Account Credit',
            self::CHEQUE => 'Cheque',
        };
    }

    public function label_bn(): string
    {
        return match ($this) {
            self::CASH => 'নগদ',
            self::BANK_TRANSFER => 'ব্যাংক স্থানান্তর',
            self::MOBILE_BANKING => 'মোবাইল ব্যাংকিং',
            self::CARD_REFUND => 'কার্ড রিফান্ড',
            self::ACCOUNT_CREDIT => 'অ্যাকাউন্ট ক্রেডিট',
            self::CHEQUE => 'চেক',
        };
    }
}
