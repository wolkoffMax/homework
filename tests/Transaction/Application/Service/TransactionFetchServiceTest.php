<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Application\Service;

use App\Account\Domain\Account;
use App\Client\Domain\Client;
use App\Transaction\Application\Exception\TransactionDataFetchFailed;
use App\Transaction\Application\Service\TransactionFetchService;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionRepository;
use App\Transaction\Domain\TransactionType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TransactionFetchServiceTest extends TestCase
{
    private TransactionFetchService $service;
    private TransactionRepository|MockObject $transactionRepositoryMock;

    protected function setUp(): void
    {
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);

        $this->service = new TransactionFetchService($this->transactionRepositoryMock);
    }

    public function testFetchesSucessfully(): void
    {
        $client = new Client('Full Name', 'username', '123456');
        $account = new Account($client, 'USD', '100.00');

        $accountId = $account->id();

        $sampleData = [
            new Transaction(TransactionType::INCOMING, $account, '25.00', $account->currency()),
        ];

        $this->transactionRepositoryMock->expects($this->once())
            ->method('findLatestByAccountId')
            ->with($accountId)
            ->willReturn($sampleData);

        $result = $this->service->fetch($accountId, 1, 10);

        $this->assertEquals($sampleData, $result);
    }

    public function testThrowsAnException(): void
    {
        $accountId = '18d9f700-41c2-4e9f-8af1-91a4f8031dd3';

        $exceptionMessage = 'Transaction data fetch failed.';
        $this->transactionRepositoryMock->expects($this->once())
            ->method('findLatestByAccountId')
            ->with($accountId)
            ->willThrowException(new RuntimeException($exceptionMessage));

        $this->expectException(TransactionDataFetchFailed::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->fetch($accountId, 1, 10);
    }
}
