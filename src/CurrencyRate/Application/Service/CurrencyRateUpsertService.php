<?php

declare(strict_types=1);

namespace App\CurrencyRate\Application\Service;

use App\CurrencyRate\Domain\CurrencyRateFactory;
use App\CurrencyRate\Domain\CurrencyRateRepository;

final class CurrencyRateUpsertService
{
    private CurrencyRateRepository $currencyRates;

    public function __construct(CurrencyRateRepository $currencyRates)
    {
        $this->currencyRates = $currencyRates;
    }

    public function upsert(CurrencyRateUpsert $command): void
    {
        $currencyRate = $this->currencyRates->findByCurrencyPair(
            $command->baseCurrency(),
            $command->targetCurrency()
        );

        if ($currencyRate) {
            $currencyRate->updateRateData(
                $command->rate(),
                $command->conversionDate()
            );

            $this->currencyRates->update($currencyRate);

            return;
        }

        $currencyRate = CurrencyRateFactory::create(
            $command->baseCurrency(),
            $command->targetCurrency(),
            $command->rate(),
            $command->conversionDate()
        );

        $this->currencyRates->add($currencyRate);
    }
}
