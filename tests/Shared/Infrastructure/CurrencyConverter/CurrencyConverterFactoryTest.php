<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\CurrencyConverter;

use App\CurrencyRate\Domain\CurrencyRate;
use App\CurrencyRate\Domain\CurrencyRateRepository;
use App\Shared\Infrastructure\CurrencyConverter\ApiCurrencyConverter;
use App\Shared\Infrastructure\CurrencyConverter\CurrencyConverterFactory;
use App\Shared\Infrastructure\CurrencyConverter\DbCurrencyConverter;
use Brick\Money\Currency;
use Brick\Money\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CurrencyConverterFactoryTest extends TestCase
{
    private CurrencyConverterFactory $factory;
    private DbCurrencyConverter|MockObject $dbCurrencyConverterMock;
    private ApiCurrencyConverter|MockObject $apiCurrencyConverterMock;
    private CurrencyRateRepository|MockObject $currencyRateRepositoryMock;

    public function setUp(): void
    {
        $this->factory = new CurrencyConverterFactory(
            $this->dbCurrencyConverterMock = $this->createMock(DbCurrencyConverter::class),
            $this->apiCurrencyConverterMock = $this->createMock(ApiCurrencyConverter::class),
            $this->currencyRateRepositoryMock = $this->createMock(CurrencyRateRepository::class)
        );
    }

    public function testConvertWithApiConverter(): void
    {
        $amount = Money::of('10', 'EUR');
        $currency = Currency::of('USD');

        $this->currencyRateRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with('EUR', 'USD')
            ->willReturn(null);

        $this->dbCurrencyConverterMock
            ->expects($this->never())
            ->method('convert');

        $this->apiCurrencyConverterMock
            ->expects($this->once())
            ->method('convert');

        $this->factory->convert($amount, $currency);
    }

    public function testConvertWithDbConverter(): void
    {
        $amount = Money::of('10', 'EUR');
        $currency = Currency::of('USD');

        $currencyRateMock = $this->createMock(CurrencyRate::class);

        $this->currencyRateRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with('EUR', 'USD')
            ->willReturn($currencyRateMock);

        $this->dbCurrencyConverterMock
            ->expects($this->once())
            ->method('convert');

        $this->apiCurrencyConverterMock
            ->expects($this->never())
            ->method('convert');

        $this->factory->convert($amount, $currency);
    }

    public function testConvertWithApiConverterCurrencyRecordOld(): void
    {
        $amount = Money::of('10', 'EUR');
        $currency = Currency::of('USD');

        $currencyRateMock = $this->createMock(CurrencyRate::class);
        $currencyRateMock->expects($this->once())
            ->method('isOlderThanOneDay')
            ->willReturn(true);

        $this->currencyRateRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with('EUR', 'USD')
            ->willReturn($currencyRateMock);

        $this->dbCurrencyConverterMock
            ->expects($this->never())
            ->method('convert');

        $this->apiCurrencyConverterMock
            ->expects($this->once())
            ->method('convert');

        $this->factory->convert($amount, $currency);
    }
}
