<?php

declare(strict_types=1);

namespace App\Tests\Account\Application\Service;

use App\Account\Application\Exception\AccountDataFetchFailed;
use App\Account\Application\Service\AccountFetchService;
use App\Account\Domain\Account;
use App\Account\Domain\AccountRepository;
use App\Client\Domain\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AccountFetchServiceTest extends TestCase
{
    private AccountFetchService $service;
    private AccountRepository|MockObject $accountRepositoryMock;

    protected function setUp(): void
    {
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);

        $this->service = new AccountFetchService($this->accountRepositoryMock);
    }

    public function testFetchesSuccessfully(): void
    {
        $client = new Client('Full Name', 'username', '123456');

        $clientId = $client->id();

        $sampleData = [
            new Account($client, 'USD', '100.00'),
        ];

        $this->accountRepositoryMock->expects($this->once())
            ->method('findAllByClientId')
            ->with($clientId)
            ->willReturn($sampleData);

        $result = $this->service->fetch($clientId);

        $this->assertEquals($sampleData, $result);
    }

    public function testThrowsAnException(): void
    {
        $clientId = '18d9f700-41c2-4e9f-8af1-91a4f8031dd3';

        $exceptionMessage = 'Account data fetch failed.';
        $this->accountRepositoryMock->expects($this->once())
            ->method('findAllByClientId')
            ->with($clientId)
            ->willThrowException(new RuntimeException($exceptionMessage));

        $this->expectException(AccountDataFetchFailed::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->fetch($clientId);
    }
}
