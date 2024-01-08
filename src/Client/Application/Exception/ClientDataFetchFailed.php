<?php

declare(strict_types=1);

namespace App\Client\Application\Exception;

use RuntimeException;
use Throwable;

final class ClientDataFetchFailed extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Client data fetch failed.', 0, $exception);
    }
}
