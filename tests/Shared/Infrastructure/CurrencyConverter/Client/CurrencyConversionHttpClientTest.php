<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\CurrencyConverter\Client;

use App\Shared\Infrastructure\CurrencyConverter\Client\CurrencyConversionHttpClient;
use App\Shared\Infrastructure\CurrencyConverter\Exception\ConvertCurrencyResponseFailed;
use App\Shared\Infrastructure\CurrencyConverter\Response\ConvertCurrencyResponse;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyConversionHttpClientTest extends TestCase
{
    private HttpClient|MockObject $httpClientMock;
    private $client;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClient::class);
        $this->client = new CurrencyConversionHttpClient($this->httpClientMock);
    }

    public function testConvertSuccessful(): void
    {
        $fakeResponse = [
            'success' => true,
            'query' => [
                'from' => 'USD',
                'to' => 'EUR',
                'amount' => 100,
            ],
            'info' => [
                'rate' => 0.855,
            ],
            'date' => '2021-09-15 17:00:02 UTC',
            'result' => 85.5,
        ];

        $this->httpClientMock->method('get')
            ->willReturn(new Response(200, [], json_encode($fakeResponse)));

        $response = $this->client->convert('USD', 'EUR', '100');

        $this->assertInstanceOf(ConvertCurrencyResponse::class, $response);
        $this->assertEquals($fakeResponse['result'], $response->result());
        $this->assertEquals($fakeResponse['info']['rate'], $response->rate());
        $this->assertEquals($fakeResponse['date'], $response->conversionDate()->format('Y-m-d H:i:s T'));
    }

    public function testConvertThrowsException(): void
    {
        $this->httpClientMock->method('get')
            ->will($this->throwException(new Exception()));

        $this->expectException(ConvertCurrencyResponseFailed::class);

        $this->client->convert('USD', 'EUR', '100');
    }
}
