<?php

declare(strict_types=1);

namespace App\Transaction\Application\Service;

use App\Transaction\Application\Exception\TransactionDataFetchFailed;
use App\Transaction\Domain\TransactionRepository;
use Throwable;

final class StatementTransactionFetchService
{
    private TransactionRepository $transactions;

    public function __construct(TransactionRepository $transactions)
    {
        $this->transactions = $transactions;
    }

    public function fetch(string $accountId, string $year, string $month): array
    {
        try {
            $transactions = $this->transactions->findLatestByAccountIdYearMonth($accountId, $year, $month);
        } catch (Throwable $exception) {
            throw TransactionDataFetchFailed::from($exception);
        }

        return $transactions;
    }
}
