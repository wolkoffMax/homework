<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

interface TransactionRepository
{
    public function findLatestByAccountId(string $accountId, int $offset, int $limit): array;

    public function findLatestByAccountIdYearMonth(string $accountId, string $year, string $month): array;

    public function add(Transaction $transaction): void;

    public function update(Transaction $transaction): void;
}
