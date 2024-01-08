<?php

declare(strict_types=1);

namespace App\Transaction\Application\Exception;

use RuntimeException;
use Throwable;

final class TransactionDataFetchFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Transaction data fetch failed.', 0, $exception);
    }
}
