<?php

declare(strict_types=1);

namespace App\Account\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class AccountBalanceUpdated extends Event
{
    public const NAME = 'account.balance.updated';

    private string $sourceAccountId;
    private string $targetAccountId;
    private string $amount;
    private ?string $convertedAmount;

    public function __construct(string $sourceAccountId, string $targetAccountId, string $amount, string $convertedAmount = null)
    {
        $this->sourceAccountId = $sourceAccountId;
        $this->targetAccountId = $targetAccountId;
        $this->amount = $amount;
        $this->convertedAmount = $convertedAmount;
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

    public function convertedAmount(): ?string
    {
        return $this->convertedAmount;
    }
}
