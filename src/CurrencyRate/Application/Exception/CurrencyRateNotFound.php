<?php

declare(strict_types=1);

namespace App\CurrencyRate\Application\Exception;

use RuntimeException;

final class CurrencyRateNotFound extends RuntimeException
{
    public static function byCurrencyPair(string $baseCurrency, string $targetCurrency): self
    {
        return new self(sprintf('Currency rate for %s/%s not found', $baseCurrency, $targetCurrency));
    }
}
