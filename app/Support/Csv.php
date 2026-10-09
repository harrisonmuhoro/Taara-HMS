<?php

namespace App\Support;

class Csv
{
    public static function safe(mixed $value): string
    {
        $s = (string) $value;

        // Neutralise cells a spreadsheet would treat as a formula
        return $s !== '' && preg_match('/^[=+\-@\t\r]/', $s) === 1
            ? "'" . $s
            : $s;
    }
}
