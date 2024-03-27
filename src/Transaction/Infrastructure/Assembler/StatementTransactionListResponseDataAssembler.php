<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Assembler;

use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionType;
use App\Transaction\Infrastructure\Dto\TransactionResponseDto;
use Brick\Money\Currency;
use Brick\Money\Money;

final class StatementTransactionListResponseDataAssembler
{
    public function assemble(array $transactions, string $accountId, string $year, string $month): array
    {
        $response = [
            'accountId' => $accountId,
            'year' => $year,
            'month' => $month,
        ];

        $creditTransactionList = [];
        $debitTransactionList = [];

        $creditTotalAmount = null;
        $debitTotalAmount = null;

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if (! $transaction instanceof Transaction) {
                throw new WrongDataTypePassed();
            }

            if (TransactionType::OUTGOING === $transaction->type()) {
                $creditTotalAmount = $this->processTransaction($creditTotalAmount, $transaction);
                $creditTransactionList[] = TransactionResponseDto::createFromTransaction($transaction);
            }

            if (TransactionType::INCOMING === $transaction->type()) {
                $debitTotalAmount = $this->processTransaction($debitTotalAmount, $transaction);
                $debitTransactionList[] = TransactionResponseDto::createFromTransaction($transaction);
            }
        }

        $response['credits']['transactions'] = $creditTransactionList;
        $response['debits']['transactions'] = $debitTransactionList;

        $response['credits']['totalAmount'] = $creditTotalAmount;
        $response['debits']['totalAmount'] = $debitTotalAmount;

        return $response;
    }

    private function processTransaction(?Money $totalAmount, Transaction $transaction): Money
    {
        return $totalAmount
            ? $totalAmount->plus(Money::of($transaction->amount(), Currency::of($transaction->currency())))
            : Money::of($transaction->amount(), Currency::of($transaction->currency()));
    }
}
