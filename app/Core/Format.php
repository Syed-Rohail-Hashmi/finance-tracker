<?php

namespace App\Core;

class Format
{
    public static function money(float $amount): string
    {
        $sign = $amount < 0 ? '-' : '';

        return $sign . APP_CURRENCY_SYMBOL . number_format(abs($amount), 2);
    }

    private function __construct()
    {
    }
}