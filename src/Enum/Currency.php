<?php

namespace App\Enum;

enum Currency: string
{
    case PLN = 'PLN';
    case USD = 'USD';
    case EUR = 'EUR';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
