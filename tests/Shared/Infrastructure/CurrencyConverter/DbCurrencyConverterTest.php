<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\CurrencyConverter;

use App\CurrencyRate\Application\Exception\CurrencyRateNotFound;
use App\CurrencyRate\Domain\CurrencyRate;
use App\CurrencyRate\Domain\CurrencyRateRepository;
use App\Shared\Infrastructure\CurrencyConverter\DbCurrencyConverter;
use Brick\Money\Currency;
use Brick\Money\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class DbCurrencyConverterTest extends TestCase
{
    private DbCurrencyConverter $converter;
    private $currencyRatesRepositoryMock;

    public function setUp(): void
    {
        $this->currencyRatesRepositoryMock = $this->createMock(CurrencyRateRepository::class);
        $this->converter = new DbCurrencyConverter($this->currencyRatesRepositoryMock);
    }

    public function testConvert(): void
    {
        $rate = 1.55;

        $amount = Money::of(10, 'EUR');
        $currency = Currency::of('USD');

        $currencyRate = new CurrencyRate(
            $amount->getCurrency()->getCurrencyCode(),
            $currency->getCurrencyCode(),
            $rate,
            new DateTimeImmutable()
        );

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with($amount->getCurrency()->getCurrencyCode(), $currency->getCurrencyCode())
            ->willReturn($currencyRate);

        $testResult = Money::of(15.5, 'USD');
        $result = $this->converter->convert($amount, $currency);

        $this->assertEquals($testResult, $result);
    }

    public function testConvertThrowsException(): void
    {
        $this->expectException(CurrencyRateNotFound::class);

        $amount = Money::of(100, 'EUR');
        $currency = Currency::of('USD');

        $this->currencyRatesRepositoryMock
            ->expects($this->once())
            ->method('findByCurrencyPair')
            ->with($amount->getCurrency()->getCurrencyCode(), $currency->getCurrencyCode())
            ->willReturn(null);

        $this->converter->convert($amount, $currency);
    }
}
