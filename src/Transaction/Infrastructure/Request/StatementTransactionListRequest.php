<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Request;

use Webmozart\Assert\Assert;

final class StatementTransactionListRequest
{
    private string $accountId;

    private string $year;

    private string $month;

    public function __construct(string $accountId, string $year, string $month)
    {
        Assert::uuid($accountId);
        Assert::numeric($year);
        Assert::length($year, 4);

        Assert::numeric($month);
        Assert::range($month, 1, 12);

        $this->accountId = $accountId;
        $this->year = $year;
        $this->month = $month;
    }

    public function accountId(): string
    {
        return $this->accountId;
    }

    public function year(): string
    {
        return $this->year;
    }

    public function month(): string
    {
        return $this->month;
    }
}
