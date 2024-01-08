<?php

declare(strict_types=1);

namespace App\Account\Application\Service;

use App\Account\Application\Event\AccountBalanceUpdated;
use App\Account\Application\Exception\AccountAmountTransferFailedUnexpectedly;
use App\Account\Application\Exception\AccountHasInsufficientFunds;
use App\Account\Application\Exception\AccountNotFound;
use App\Account\Domain\AccountRepository;
use App\Shared\Application\CurrencyConverter\CurrencyConverter;
use Brick\Money\Currency;
use Brick\Money\Money;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class AccountAmountTransferService
{
    private AccountRepository $accounts;
    private CurrencyConverter $currencyConverter;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        AccountRepository $accounts,
        CurrencyConverter $currencyConverter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->accounts = $accounts;
        $this->currencyConverter = $currencyConverter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function transfer(AccountAmountTransfer $command): void
    {
        $this->accounts->beginTransaction();

        try {
            $sourceAccount = $this->accounts->findById($command->sourceAccountId());

            if (! $sourceAccount) {
                throw AccountNotFound::byId($command->sourceAccountId());
            }

            $targetAccount = $this->accounts->findById($command->targetAccountId());

            if (! $targetAccount) {
                throw AccountNotFound::byId($command->targetAccountId());
            }

            $sourceBalance = Money::of($sourceAccount->balance(), Currency::of($sourceAccount->currency()));
            $targetBalance = Money::of($targetAccount->balance(), Currency::of($targetAccount->currency()));
            $amount = Money::of($command->amount(), Currency::of($sourceAccount->currency()));

            if ($sourceBalance->isLessThan($amount)) {
                throw AccountHasInsufficientFunds::byId($command->sourceAccountId());
            }

            if (! $sourceBalance->getCurrency()->is($targetBalance->getCurrency())) {
                $convertedAmount = $this->currencyConverter->convert($amount, Currency::of($targetAccount->currency()));
            }

            $sourceBalance = $sourceBalance->minus($amount);
            $targetBalance = $targetBalance->plus($convertedAmount ?? $amount);

            $sourceAccount->setBalance((string) $sourceBalance->getAmount());
            $targetAccount->setBalance((string) $targetBalance->getAmount());

            $this->accounts->update($sourceAccount);
            $this->accounts->update($targetAccount);

            $this->accounts->commit();

            $event = new AccountBalanceUpdated(
                $sourceAccount->id(),
                $targetAccount->id(),
                (string) $amount->getAmount(),
                $convertedAmount ? (string) $convertedAmount->getAmount() : null
            );

            $this->eventDispatcher->dispatch($event);
        } catch (AccountNotFound|AccountHasInsufficientFunds $exception) {
            $this->accounts->rollback();

            throw $exception;
        } catch (Throwable $exception) {
            $this->accounts->rollback();

            throw AccountAmountTransferFailedUnexpectedly::from($exception);
        }
    }
}
