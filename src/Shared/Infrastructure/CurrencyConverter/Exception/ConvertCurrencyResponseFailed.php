<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CurrencyConverter\Exception;

use RuntimeException;
use Throwable;

final class ConvertCurrencyResponseFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Convert currency response failed', 0, $exception);
    }
}
