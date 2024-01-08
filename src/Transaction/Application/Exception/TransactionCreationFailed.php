<?php

declare(strict_types=1);

namespace App\Transaction\Application\Exception;

use RuntimeException;
use Throwable;

final class TransactionCreationFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Transaction creation failed.', 0, $exception);
    }
}
