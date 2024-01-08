<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter\Response;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

final class ConvertCurrencyResponse
{
    private bool $success;
    private ?float $rate = null;
    private ?string $date = null;
    private ?float $result = null;

    public function __construct(array $responseData)
    {
        Assert::keyExists($responseData, 'success');

        $this->success = $responseData['success'];

        if ($this->success) {
            Assert::keyExists($responseData, 'info');
            Assert::keyExists($responseData['info'], 'rate');
            Assert::keyExists($responseData, 'date');
            Assert::keyExists($responseData, 'result');

            Assert::float($responseData['info']['rate']);
            Assert::string($responseData['date']);
            Assert::float($responseData['result']);

            $this->rate = $responseData['info']['rate'];
            $this->date = $responseData['date'];
            $this->result = $responseData['result'];
        }
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function rate(): ?float
    {
        return $this->rate;
    }

    public function conversionDate(): ?DateTimeImmutable
    {
        return $this->date ? new DateTimeImmutable($this->date) : null;
    }

    public function result(): ?float
    {
        return $this->result;
    }
}
