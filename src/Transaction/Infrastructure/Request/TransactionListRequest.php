<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Request;

use Webmozart\Assert\Assert;

final class TransactionListRequest
{
    private string $accountId;
    private int $page;
    private int $limit;

    public function __construct(string $accountId, int $page, int $limit)
    {
        Assert::uuid($accountId);
        Assert::greaterThan($page, 0);
        Assert::greaterThan($limit, 0);

        $this->accountId = $accountId;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function accountId(): string
    {
        return $this->accountId;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }
}
