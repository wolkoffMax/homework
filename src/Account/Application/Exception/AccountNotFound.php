<?php

declare(strict_types=1);

namespace App\Account\Application\Exception;

use RuntimeException;

final class AccountNotFound extends RuntimeException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Account with id %s not found', $id));
    }
}
