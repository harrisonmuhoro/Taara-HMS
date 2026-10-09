<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public static function toMinor(string|int|float|null $amount): int
    {
        $value = trim((string) ($amount ?? '0'));
        if (! preg_match('/^-?\d+(?:\.\d{1,2})?$/', $value)) {
            throw new InvalidArgumentException('Invalid monetary amount.');
        }

        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '+-');
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $minor = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');

        return $negative ? -$minor : $minor;
    }

    public static function fromMinor(int $minor): string
    {
        $negative = $minor < 0;
        $absolute = abs($minor);
        $formatted = intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);

        return $negative ? '-'.$formatted : $formatted;
    }

    /** Percentage values use hundredths of a percent: 16.50% becomes 1650. */
    public static function percentageBasisPoints(string|int|float|null $percentage): int
    {
        return self::toMinor($percentage);
    }

    public static function percentageOf(int $minor, int $percentageBasisPoints): int
    {
        $numerator = $minor * $percentageBasisPoints;

        return intdiv($numerator + ($numerator >= 0 ? 5000 : -5000), 10000);
    }
}
