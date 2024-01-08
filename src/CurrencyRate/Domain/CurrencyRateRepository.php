<?php

declare(strict_types=1);

namespace App\CurrencyRate\Domain;

interface CurrencyRateRepository
{
    public function findByCurrencyPair(string $baseCurrency, string $targetCurrency): ?CurrencyRate;

    public function add(CurrencyRate $currencyRate): void;

    public function update(CurrencyRate $currencyRate): void;
}
