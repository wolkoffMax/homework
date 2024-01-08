<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter\Client;

use App\Shared\Infrastructure\CurrencyConverter\Exception\ConvertCurrencyResponseFailed;
use App\Shared\Infrastructure\CurrencyConverter\Response\ConvertCurrencyResponse;
use GuzzleHttp\Client as HttpClient;
use Throwable;

final class CurrencyConversionHttpClient
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function convert(string $baseCurrency, string $targetCurrency, string $amount): ConvertCurrencyResponse
    {
        try {
            $response = $this->httpClient->get('/exchangerates_data/convert', [
                'query' => [
                    'from' => $baseCurrency,
                    'to' => $targetCurrency,
                    'amount' => $amount,
                ],
            ]);
        } catch (Throwable $exception) {
            throw ConvertCurrencyResponseFailed::from($exception);
        }

        return new ConvertCurrencyResponse(json_decode($response->getBody()->getContents(), true));
    }
}
