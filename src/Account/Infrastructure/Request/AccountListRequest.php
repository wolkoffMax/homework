<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Request;

use Webmozart\Assert\Assert;

final class AccountListRequest
{
    private string $clientId;

    public function __construct(string $clientId)
    {
        Assert::uuid($clientId);

        $this->clientId = $clientId;
    }

    public function clientId(): string
    {
        return $this->clientId;
    }
}
