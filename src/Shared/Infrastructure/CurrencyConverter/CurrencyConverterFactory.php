<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter;

use App\CurrencyRate\Domain\CurrencyRateRepository;
use App\Shared\Application\CurrencyConverter\CurrencyConverter;
use Brick\Money\Currency;
use Brick\Money\Money;

final class CurrencyConverterFactory implements CurrencyConverter
{
    private DbCurrencyConverter $dbCurrencyConverter;
    private ApiCurrencyConverter $apiCurrencyConverter;
    private CurrencyRateRepository $currencyRates;

    public function __construct(
        DbCurrencyConverter $dbCurrencyConverter,
        ApiCurrencyConverter $apiCurrencyConverter,
        CurrencyRateRepository $currencyRates
    ) {
        $this->dbCurrencyConverter = $dbCurrencyConverter;
        $this->apiCurrencyConverter = $apiCurrencyConverter;
        $this->currencyRates = $currencyRates;
    }

    public function convert(Money $amount, Currency $targetCurrency): Money
    {
        $currencyRate = $this->currencyRates->findByCurrencyPair(
            $amount->getCurrency()->getCurrencyCode(),
            $targetCurrency->getCurrencyCode()
        );

        if (! $currencyRate || $currencyRate->isOlderThanOneDay()) {
            return $this->apiCurrencyConverter->convert($amount, $targetCurrency);
        }

        return $this->dbCurrencyConverter->convert($amount, $targetCurrency);
    }
}
