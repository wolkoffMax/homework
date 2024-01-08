<?php

declare(strict_types=1);

namespace App\Transaction\Application\EventListener;

use App\Account\Application\Event\AccountBalanceUpdated;
use App\Account\Application\Exception\AccountNotFound;
use App\Account\Domain\AccountRepository;
use App\Transaction\Application\Exception\TransactionCreationFailed;
use App\Transaction\Domain\TransactionFactory;
use App\Transaction\Domain\TransactionRepository;
use App\Transaction\Domain\TransactionType;
use Throwable;

final class CreateTransactionEventListener
{
    private AccountRepository $accounts;
    private TransactionRepository $transactions;

    public function __construct(AccountRepository $accounts, TransactionRepository $transactions)
    {
        $this->accounts = $accounts;
        $this->transactions = $transactions;
    }

    public function onAccountBalanceUpdated(AccountBalanceUpdated $event): void
    {
        try {
            $sourceAccount = $this->accounts->findById($event->sourceAccountId());

            if (! $sourceAccount) {
                throw AccountNotFound::byId($event->sourceAccountId());
            }

            $targetAccount = $this->accounts->findById($event->targetAccountId());

            if (! $targetAccount) {
                throw AccountNotFound::byId($event->targetAccountId());
            }

            $outgoingTransaction = TransactionFactory::create(
                TransactionType::OUTGOING,
                $sourceAccount,
                $event->amount(),
                $sourceAccount->currency()
            );

            $incomingTransaction = TransactionFactory::create(
                TransactionType::INCOMING,
                $targetAccount,
                $event->convertedAmount() ?: $event->amount(),
                $targetAccount->currency()
            );

            $this->transactions->add($outgoingTransaction);
            $this->transactions->add($incomingTransaction);
        } catch (Throwable $exception) {
            throw TransactionCreationFailed::from($exception);
        }
    }
}
