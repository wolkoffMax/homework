<?php

declare(strict_types=1);

namespace App\CurrencyRate\Application\Exception;

use RuntimeException;
use Throwable;

final class CurrencyUpdateFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Currency update failed', 0, $exception);
    }
}
