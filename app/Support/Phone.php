<?php

namespace App\Support;

class Phone
{
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '234') && strlen($digits) >= 13) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '+234'.substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            return '+234'.$digits;
        }

        return '+'.$digits;
    }

    public static function isValid(?string $value): bool
    {
        $normalized = self::normalize($value);

        return (bool) preg_match('/^\+234\d{10}$|^\+\d{10,15}$/', $normalized);
    }
}
