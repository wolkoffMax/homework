<?php

declare(strict_types=1);

namespace App\Account\Application\Service;

final class AccountAmountTransfer
{
    private string $sourceAccountId;
    private string $targetAccountId;
    private string $amount;

    public function __construct(string $sourceAccountId, string $targetAccountId, string $amount)
    {
        $this->sourceAccountId = $sourceAccountId;
        $this->targetAccountId = $targetAccountId;
        $this->amount = $amount;
    }

    public function sourceAccountId(): string
    {
        return $this->sourceAccountId;
    }

    public function targetAccountId(): string
    {
        return $this->targetAccountId;
    }

    public function amount(): string
    {
        return $this->amount;
    }
}
