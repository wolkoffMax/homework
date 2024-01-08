<?php

declare(strict_types=1);

namespace App\CurrencyRate\Domain;

use App\Shared\Domain\Service\UuidService;
use App\Shared\Domain\TimeStampableTrait;
use DateTimeImmutable;

final class CurrencyRate
{
    use TimeStampableTrait;

    private string $id;

    private string $baseCurrency;

    private string $targetCurrency;

    private float $rate;

    private DateTimeImmutable $conversionDate;

    public function __construct(string $baseCurrency, string $targetCurrency, float $rate, DateTimeImmutable $conversionDate)
    {
        $this->id = UuidService::generate();
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->rate = $rate;
        $this->conversionDate = $conversionDate;
    }

    public function id(): string
    {
        return $this->id;
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

    public function updateRateData(float $rate, DateTimeImmutable $conversionDate): void
    {
        $this->rate = $rate;
        $this->conversionDate = $conversionDate;
    }

    public function isOlderThanOneDay(): bool
    {
        return $this->conversionDate->diff(new DateTimeImmutable())->days > 1;
    }

    public function prePersist(): void
    {
        $this->updateTimestamps();
    }
}
