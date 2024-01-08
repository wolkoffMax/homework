<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter\Exception;

use RuntimeException;
use Throwable;

final class ConversionRequestFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Conversion request failed', 0, $exception);
    }
}
