<?php

declare(strict_types=1);

namespace App\Tests\Client\Application\Service;

use App\Client\Application\Exception\ClientDataFetchFailed;
use App\Client\Application\Service\ClientsFetchService;
use App\Client\Domain\Client;
use App\Client\Domain\ClientRepository;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ClientsFetchServiceTest extends TestCase
{
    private ClientsFetchService $clientFetchService;
    private ClientRepository $clientRepository;

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepository::class);

        $this->clientFetchService = new ClientsFetchService($this->clientRepository);
    }

    public function testFetchesSucessfully(): void
    {
        $sampleData = [
            new Client('Full Name', 'username', '123456'),
        ];

        $this->clientRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($sampleData);

        $result = $this->clientFetchService->fetch();

        $this->assertEquals($sampleData, $result);
    }

    public function testThrowsAnException(): void
    {
        $exceptionMessage = 'Client data fetch failed.';

        $this->clientRepository->expects($this->once())
            ->method('findAll')
            ->willThrowException(new RuntimeException($exceptionMessage));

        $this->expectException(ClientDataFetchFailed::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->clientFetchService->fetch();
    }
}
