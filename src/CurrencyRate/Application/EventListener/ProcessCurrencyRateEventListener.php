<?php

declare(strict_types=1);

namespace App\CurrencyRate\Application\EventListener;

use App\CurrencyRate\Application\Exception\CurrencyUpdateFailed;
use App\CurrencyRate\Application\Service\CurrencyRateUpsert;
use App\CurrencyRate\Application\Service\CurrencyRateUpsertService;
use App\CurrencyRate\Domain\Event\NewCurrencyRateReceived;
use Throwable;

final class ProcessCurrencyRateEventListener
{
    private CurrencyRateUpsertService $upsertService;

    public function __construct(CurrencyRateUpsertService $upsertService)
    {
        $this->upsertService = $upsertService;
    }

    public function onNewCurrencyRateReceived(NewCurrencyRateReceived $event): void
    {
        try {
            $command = new CurrencyRateUpsert(
                $event->baseCurrency(),
                $event->targetCurrency(),
                $event->rate(),
                $event->conversionDate()
            );

            $this->upsertService->upsert($command);
        } catch (Throwable $exception) {
            throw CurrencyUpdateFailed::from($exception);
        }
    }
}
