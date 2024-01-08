<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter;

use App\CurrencyRate\Application\Exception\CurrencyRateNotFound;
use App\CurrencyRate\Domain\CurrencyRateRepository;
use App\Shared\Application\CurrencyConverter\CurrencyConverter;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;

final class DbCurrencyConverter implements CurrencyConverter
{
    private CurrencyRateRepository $currencyRates;

    public function __construct(CurrencyRateRepository $currencyRates)
    {
        $this->currencyRates = $currencyRates;
    }

    public function convert(Money $amount, Currency $targetCurrency): Money
    {
        $currencyRate = $this->currencyRates->findByCurrencyPair(
            $amount->getCurrency()->getCurrencyCode(),
            $targetCurrency->getCurrencyCode()
        );

        if (! $currencyRate) {
            throw new CurrencyRateNotFound('Currency rate not found.');
        }

        $convertedAmount = $amount->multipliedBy($currencyRate->rate(), RoundingMode::HALF_UP);

        return Money::of($convertedAmount->getAmount(), $targetCurrency);
    }
}
