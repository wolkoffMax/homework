<?php

declare(strict_types=1);

namespace App\Tests\Account\Application\Service;

use App\Account\Application\Event\AccountBalanceUpdated;
use App\Account\Application\Exception\AccountAmountTransferFailedUnexpectedly;
use App\Account\Application\Exception\AccountHasInsufficientFunds;
use App\Account\Application\Exception\AccountNotFound;
use App\Account\Application\Service\AccountAmountTransfer;
use App\Account\Application\Service\AccountAmountTransferService;
use App\Account\Domain\Account;
use App\Account\Domain\AccountRepository;
use App\Client\Domain\Client;
use App\Shared\Application\CurrencyConverter\CurrencyConverter;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class AccountAmountTransferServiceTest extends TestCase
{
    private AccountAmountTransferService $service;
    private AccountRepository|MockObject $accountRepositoryMock;
    private CurrencyConverter|MockObject $currencyConverterMock;
    private EventDispatcherInterface|MockObject $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->service = new AccountAmountTransferService(
            $this->accountRepositoryMock,
            $this->currencyConverterMock,
            $this->eventDispatcherMock
        );
    }

    public function itTransfersCorrectly(): void
    {
        $sourceClient = new Client('Full Name', 'username', '123456');
        $targetClient = new Client('Full Name', 'username', '123456');

        $amount = '10.00';

        $sourceAccount = new Account($sourceClient, 'USD', '50.00');
        $targetAccount = new Account($targetClient, 'USD', '25.00');

        $sourceAccountId = $sourceAccount->id();
        $targetAccountId = $targetAccount->id();

        $this->accountRepositoryMock->expects($this->exactly(2))
            ->method('findById')
            ->willReturnOnConsecutiveCalls($sourceAccount, $targetAccount);

        $this->currencyConverterMock
            ->expects($this->never())
            ->method('convert');

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(AccountBalanceUpdated::class));

        $this->service->transfer(
            new AccountAmountTransfer($sourceAccountId, $targetAccountId, $amount)
        );

        $this->assertEquals('40.00', $sourceAccount->balance());
        $this->assertEquals('35.00', $targetAccount->balance());
    }

    public function testTransferAccountNotFound(): void
    {
        $this->expectException(AccountNotFound::class);

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->accountRepositoryMock
            ->expects($this->never())
            ->method('update');

        $this->accountRepositoryMock
            ->expects($this->never())
            ->method('commit');

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('rollback');

        $sourceAccountId = 'a9b8dd3f-df54-43ba-a327-50450758ce36';
        $targetAccountId = '0d54c865-41da-426e-928a-c2d790666b2a';
        $amount = '25.00';

        $this->service->transfer(
            new AccountAmountTransfer($sourceAccountId, $targetAccountId, $amount)
        );
    }

    public function testTransferAccountTransferFailedUnexpectedly(): void
    {
        $this->expectException(AccountAmountTransferFailedUnexpectedly::class);

        $sourceClient = new Client('Full Name', 'username', '123456');
        $targetClient = new Client('Full Name', 'username', '123456');

        $sourceAccount = new Account($sourceClient, 'USD', '50.00');
        $targetAccount = new Account($targetClient, 'USD', '25.00');

        $this->accountRepositoryMock
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnOnConsecutiveCalls($sourceAccount, $targetAccount);

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new Exception('Something happened.'));

        $this->accountRepositoryMock
            ->expects($this->never())
            ->method('commit');

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('rollback');

        $sourceAccountId = 'a9b8dd3f-df54-43ba-a327-50450758ce36';
        $targetAccountId = '0d54c865-41da-426e-928a-c2d790666b2a';
        $amount = '25.00';

        $this->service->transfer(
            new AccountAmountTransfer($sourceAccountId, $targetAccountId, $amount)
        );
    }

    public function testTransferAccountHasInsufficientFunds(): void
    {
        $this->expectException(AccountHasInsufficientFunds::class);

        $sourceClient = new Client('Full Name', 'username', '123456');
        $targetClient = new Client('Full Name', 'username', '123456');

        $sourceAccount = new Account($sourceClient, 'USD', '50.00');
        $targetAccount = new Account($targetClient, 'USD', '25.00');

        $this->accountRepositoryMock
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnOnConsecutiveCalls($sourceAccount, $targetAccount);

        $this->accountRepositoryMock
            ->expects($this->never())
            ->method('update')
            ->willThrowException(new Exception('Something happened.'));

        $this->accountRepositoryMock
            ->expects($this->never())
            ->method('commit');

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('rollback');

        $sourceAccountId = 'a9b8dd3f-df54-43ba-a327-50450758ce36';
        $targetAccountId = '0d54c865-41da-426e-928a-c2d790666b2a';
        $amount = '75.00';

        $this->service->transfer(
            new AccountAmountTransfer($sourceAccountId, $targetAccountId, $amount)
        );
    }
}
