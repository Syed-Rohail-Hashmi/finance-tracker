<?php

namespace App\Core;

use DateTime;

class Validator
{
    public static function required($value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function minLength(string $value, int $min): bool
    {
        return mb_strlen($value) >= $min;
    }

    public static function isStrongPassword(string $value): bool
    {
        if (mb_strlen($value) < 8) {
            return false;
        }

       if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        if (!preg_match('/[^A-Za-z0-9\s]/', $value)) {
            return false;
        }

        return true;
    }

    public static function matches(string $value, string $other): bool
    {
        return $value === $other;
    }

    public static function isPositiveNumber($value): bool
    {
        return is_numeric($value) && (float) $value > 0;
    }

    public static function isPositiveInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }

    public static function isValidDate(string $value, string $format = 'Y-m-d'): bool
    {
        $date = DateTime::createFromFormat($format, $value);

        return $date !== false && $date->format($format) === $value;
    }

    private function __construct()
    {
    }
}