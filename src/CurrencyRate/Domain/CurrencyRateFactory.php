<?php

declare(strict_types=1);

namespace App\CurrencyRate\Domain;

use DateTimeImmutable;

final class CurrencyRateFactory
{
    public static function create(
        string $baseCurrency,
        string $targetCurrency,
        float $rate,
        DateTimeImmutable $conversionDate
    ): CurrencyRate {
        return new CurrencyRate($baseCurrency, $targetCurrency, $rate, $conversionDate);
    }
}
