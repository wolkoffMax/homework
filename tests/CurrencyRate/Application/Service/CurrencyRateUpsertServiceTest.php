<?php

declare(strict_types=1);

namespace App\Tests\CurrencyRate\Application\Service;

use App\CurrencyRate\Application\Service\CurrencyRateUpsert;
use App\CurrencyRate\Application\Service\CurrencyRateUpsertService;
use App\CurrencyRate\Domain\CurrencyRate;
use App\CurrencyRate\Domain\CurrencyRateRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CurrencyRateUpsertServiceTest extends TestCase
{
    private CurrencyRateRepository $currencyRatesRepositoryMock;
    private CurrencyRateUpsertService $service;

    public function setUp(): void
    {
        $this->currencyRatesRepositoryMock = $this->createMock(CurrencyRateRepository::class);
        $this->service = new CurrencyRateUpsertService($this->currencyRatesRepositoryMock);
    }

    public function testUpsertWithoutUpdate(): void
    {
        $baseCurrency = 'EUR';
        $targetCurrency = 'USD';
        $rate = 1.1234;
        $conversionDate = new DateTimeImmutable();

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with($baseCurrency, $targetCurrency)
            ->willReturn(null);

        $this->currencyRatesRepositoryMock
            ->expects($this->never())
            ->method('update');

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(CurrencyRate::class));

        $this->service->upsert(new CurrencyRateUpsert(
            $baseCurrency,
            $targetCurrency,
            $rate,
            $conversionDate
        ));
    }

    public function testUpsertWithUpdate(): void
    {
        $baseCurrency = 'EUR';
        $targetCurrency = 'USD';
        $rate = 1.1234;
        $conversionDate = new DateTimeImmutable();

        $currencyRate = $this->createMock(CurrencyRate::class);
        $currencyRate->method('rate')->willReturn(1.0128);
        $currencyRate->method('conversionDate')->willReturn(new DateTimeImmutable('-1 day'));

        $currencyRate->expects($this->once())
            ->method('updateRateData')
            ->with($rate, $conversionDate);

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with($baseCurrency, $targetCurrency)
            ->willReturn($currencyRate);

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($currencyRate);

        $this->currencyRatesRepositoryMock
            ->expects($this->never())
            ->method('add');

        $this->service->upsert(new CurrencyRateUpsert(
            $baseCurrency,
            $targetCurrency,
            $rate,
            $conversionDate
        ));
    }
}
