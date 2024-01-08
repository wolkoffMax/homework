<?php

declare(strict_types=1);

namespace App\CurrencyRate\Application\Service;

use DateTimeImmutable;

final class CurrencyRateUpsert
{
    private string $baseCurrency;

    private string $targetCurrency;

    private float $rate;

    private DateTimeImmutable $conversionDate;

    public function __construct(string $baseCurrency, string $targetCurrency, float $rate, DateTimeImmutable $conversionDate)
    {
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->rate = $rate;
        $this->conversionDate = $conversionDate;
    }

    public function baseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function targetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function rate(): float
    {
        return $this->rate;
    }

    public function conversionDate(): DateTimeImmutable
    {
        return $this->conversionDate;
    }
}
