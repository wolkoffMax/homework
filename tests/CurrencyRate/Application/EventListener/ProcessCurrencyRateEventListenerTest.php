<?php

declare(strict_types=1);

namespace App\Tests\CurrencyRate\Application\EventListener;

use App\CurrencyRate\Application\EventListener\ProcessCurrencyRateEventListener;
use App\CurrencyRate\Application\Exception\CurrencyUpdateFailed;
use App\CurrencyRate\Application\Service\CurrencyRateUpsertService;
use App\CurrencyRate\Domain\Event\NewCurrencyRateReceived;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ProcessCurrencyRateEventListenerTest extends TestCase
{
    private ProcessCurrencyRateEventListener $eventListener;
    private CurrencyRateUpsertService|MockObject $upsetServiceMock;

    public function setUp(): void
    {
        $this->upsetServiceMock = $this->createMock(CurrencyRateUpsertService::class);
        $this->eventListener = new ProcessCurrencyRateEventListener($this->upsetServiceMock);
    }

    public function testOnNewCurrencyRateReceived(): void
    {
        $this->upsetServiceMock
            ->expects($this->once())
            ->method('upsert');

        $event = new NewCurrencyRateReceived(
            'EUR',
            'USD',
            1.1234,
            new DateTimeImmutable()
        );

        $this->eventListener->onNewCurrencyRateReceived($event);
    }

    public function testOnNewCurrencyRateReceivedThrowsException(): void
    {
        $this->expectException(CurrencyUpdateFailed::class);

        $this->upsetServiceMock
            ->expects($this->once())
            ->method('upsert')
            ->willThrowException(new Exception('Something went wrong.'));

        $event = new NewCurrencyRateReceived(
            'EUR',
            'USD',
            1.1234,
            new DateTimeImmutable()
        );

        $this->eventListener->onNewCurrencyRateReceived($event);
    }
}
