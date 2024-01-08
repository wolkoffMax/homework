<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\CurrencyConverter\Response;

use App\Shared\Infrastructure\CurrencyConverter\Response\ConvertCurrencyResponse;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException as AssertInvalidArgumentException;

class ConvertCurrencyResponseTest extends TestCase
{
    public function testSuccessfulResponse(): void
    {
        $responseData = [
            'success' => true,
            'info' => ['rate' => 1.5],
            'date' => '2021-01-01',
            'result' => 150.0,
        ];

        $response = new ConvertCurrencyResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(1.5, $response->rate());
        $this->assertEquals(new DateTimeImmutable('2021-01-01'), $response->conversionDate());
        $this->assertEquals(150.0, $response->result());
    }

    public function testResponseWithMissingKeys(): void
    {
        $this->expectException(AssertInvalidArgumentException::class);

        new ConvertCurrencyResponse(['success' => true]);
    }

    public function testResponseWithInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ConvertCurrencyResponse([
            'success' => true,
            'info' => ['rate' => 'invalid'], // Invalid type
            'date' => 12345, // Invalid type
            'result' => 'invalid', // Invalid type
        ]);
    }

    public function testUnsuccessfulResponse(): void
    {
        $responseData = ['success' => false];

        $response = new ConvertCurrencyResponse($responseData);

        $this->assertFalse($response->isSuccess());
        $this->assertNull($response->rate());
        $this->assertNull($response->conversionDate());
        $this->assertNull($response->result());
    }
}
