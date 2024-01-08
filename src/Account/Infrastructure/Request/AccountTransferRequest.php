<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Request;

use Webmozart\Assert\Assert;

final class AccountTransferRequest
{
    private string $sourceAccountId;
    private string $targetAccountId;
    private string $amount;

    public function __construct(array $data)
    {
        Assert::keyExists($data, 'sourceAccountId');
        Assert::keyExists($data, 'targetAccountId');
        Assert::keyExists($data, 'amount');

        $sourceAccountId = $data['sourceAccountId'];
        $targetAccountId = $data['targetAccountId'];
        $amount = $data['amount'];

        Assert::uuid($sourceAccountId);
        Assert::uuid($targetAccountId);
        Assert::numeric($amount);
        Assert::greaterThan($amount, 0);

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
