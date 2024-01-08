<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Application\EventListener;

use App\Account\Application\Event\AccountBalanceUpdated;
use App\Account\Domain\Account;
use App\Account\Domain\AccountRepository;
use App\Client\Domain\Client;
use App\Transaction\Application\EventListener\CreateTransactionEventListener;
use App\Transaction\Application\Exception\TransactionCreationFailed;
use App\Transaction\Domain\TransactionRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateTransactionEventListenerTest extends TestCase
{
    private CreateTransactionEventListener $eventListener;
    private AccountRepository|MockObject $accountRepositoryMock;
    private TransactionRepository|MockObject $transactionRepositoryMock;

    public function setUp(): void
    {
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);

        $this->eventListener = new CreateTransactionEventListener(
            $this->accountRepositoryMock,
            $this->transactionRepositoryMock
        );
    }

    public function testOnAccountBalanceUpdatedTransactionCreationFailed()
    {
        $this->expectException(TransactionCreationFailed::class);

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->transactionRepositoryMock
            ->expects($this->never())
            ->method('add');

        $sourceAccountId = 'b39bf1ac-8925-405d-ba9c-f3c22218e9ac';
        $targetAccountId = 'b39bf1ac-8925-405d-ba9c-f3c22218e9ac';

        $event = new AccountBalanceUpdated(
            $sourceAccountId,
            $targetAccountId,
            '10.00'
        );

        $this->eventListener->onAccountBalanceUpdated($event);
    }

    public function testOnAccountBalanceUpdated()
    {
        $client = new Client('Full Name', 'username', '123456');

        $sourceAccount = new Account($client, 'USD', '50.00');
        $targetAccount = new Account($client, 'USD', '150.00');

        $this->accountRepositoryMock
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnOnConsecutiveCalls($sourceAccount, $targetAccount);

        $this->transactionRepositoryMock
            ->expects($this->exactly(2))
            ->method('add');

        $sourceAccountId = $sourceAccount->id();
        $targetAccountId = $targetAccount->id();

        $event = new AccountBalanceUpdated(
            $sourceAccountId,
            $targetAccountId,
            '10.00'
        );

        $this->eventListener->onAccountBalanceUpdated($event);
    }
}
