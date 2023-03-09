<?php

declare(strict_types=1);

namespace Plejzner\CommissionTask\Domain;

enum EUCountryCodeEnum
{
    private const EU_COUNTRY_CODES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];

    public static function contains(string $countryCode): bool
    {
        return in_array($countryCode, self::EU_COUNTRY_CODES, true);
    }
}
