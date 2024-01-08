<?php

declare(strict_types=1);

namespace App\Account\Application\Exception;

use RuntimeException;
use Throwable;

final class AccountAmountTransferFailedUnexpectedly extends RuntimeException
{
    public static function from(Throwable $exception): self
    {
        return new self('Account amount transfer failed unexpectedly', 0, $exception);
    }
}
