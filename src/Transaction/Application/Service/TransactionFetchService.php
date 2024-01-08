<?php

declare(strict_types=1);

namespace App\Transaction\Application\Service;

use App\Transaction\Application\Exception\TransactionDataFetchFailed;
use App\Transaction\Domain\TransactionRepository;
use Throwable;

final class TransactionFetchService
{
    private TransactionRepository $transactions;

    public function __construct(TransactionRepository $transactions)
    {
        $this->transactions = $transactions;
    }

    public function fetch(string $accountId, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        try {
            $transactions = $this->transactions->findLatestByAccountId($accountId, $offset, $limit);
        } catch (Throwable $exception) {
            throw TransactionDataFetchFailed::from($exception);
        }

        return $transactions;
    }
}
