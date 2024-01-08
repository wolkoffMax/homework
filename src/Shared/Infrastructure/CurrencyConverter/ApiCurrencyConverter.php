<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter;

use App\CurrencyRate\Domain\Event\NewCurrencyRateReceived;
use App\Shared\Application\CurrencyConverter\CurrencyConverter;
use App\Shared\Infrastructure\CurrencyConverter\Client\CurrencyConversionHttpClient as HttpClient;
use App\Shared\Infrastructure\CurrencyConverter\Exception\ConversionClientResponseUnsuccessful;
use App\Shared\Infrastructure\CurrencyConverter\Exception\ConversionRequestFailed;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class ApiCurrencyConverter implements CurrencyConverter
{
    private HttpClient $httpClient;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(HttpClient $httpClient, EventDispatcherInterface $eventDispatcher)
    {
        $this->httpClient = $httpClient;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function convert(Money $amount, Currency $targetCurrency): Money
    {
        try {
            $response = $this->httpClient->convert(
                $amount->getCurrency()->getCurrencyCode(),
                $targetCurrency->getCurrencyCode(),
                (string) $amount->getAmount()
            );

            if (! $response->isSuccess()) {
                throw new ConversionClientResponseUnsuccessful();
            }

            $this->eventDispatcher->dispatch(
                new NewCurrencyRateReceived(
                    $amount->getCurrency()->getCurrencyCode(),
                    $targetCurrency->getCurrencyCode(),
                    $response->rate(),
                    $response->conversionDate()
                )
            );

            return Money::of($response->result(), $targetCurrency, null, RoundingMode::HALF_UP);
        } catch (Throwable $exception) {
            throw ConversionRequestFailed::from($exception);
        }
    }
}
