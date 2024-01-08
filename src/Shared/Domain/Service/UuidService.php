<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

use Ramsey\Uuid\Uuid;

final class UuidService
{
    public static function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
