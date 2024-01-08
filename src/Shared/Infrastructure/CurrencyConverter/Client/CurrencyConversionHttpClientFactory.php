<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter\Client;

use GuzzleHttp\Client as HttpClient;

final class CurrencyConversionHttpClientFactory
{
    public function create(): CurrencyConversionHttpClient
    {
        $client = new HttpClient([
            'base_uri' => $_ENV['EXCHANGE_RATE_SERVICE_URL'],
            'headers' => [
                'apikey' => $_ENV['EXCHANGE_RATE_SERVICE_API_KEY'],
            ],
        ]);

        return new CurrencyConversionHttpClient($client);
    }
}
