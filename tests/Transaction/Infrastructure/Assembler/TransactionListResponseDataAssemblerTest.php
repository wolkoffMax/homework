<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Infrastructure\Assembler;

use App\Account\Domain\Account;
use App\Client\Domain\Client;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionType;
use App\Transaction\Infrastructure\Assembler\TransactiontListResponseDataAssembler;
use App\Transaction\Infrastructure\Dto\TransactionResponseDto;
use PHPUnit\Framework\TestCase;

final class TransactionListResponseDataAssemblerTest extends TestCase
{
    private TransactiontListResponseDataAssembler $transactionListResponseDataAssembler;

    protected function setUp(): void
    {
        $this->transactionListResponseDataAssembler = new TransactiontListResponseDataAssembler();
    }

    public function testAssemblesSuccessfully(): void
    {
        $account = $this->createMock(Account::class);
        $account->method('id')->willReturn('18d9f700-41c2-4e9f-8af1-91a4f8031dd3');

        $transaction1 = new Transaction(TransactionType::INCOMING, $account, 'USD', '10.00');
        $transaction1->updateTimestamps();

        $transaction2 = new Transaction(TransactionType::OUTGOING, $account, 'GBP', '25.00');
        $transaction2->updateTimestamps();

        $accounts = [$transaction1, $transaction2];

        $result = $this->transactionListResponseDataAssembler->assemble($accounts);

        $this->assertCount(2, $result);

        foreach ($result as $item) {
            $this->assertInstanceOf(TransactionResponseDto::class, $item);
        }
    }

    public function testThrowsAnException(): void
    {
        $invalidData = [
            new Client('Full Name', 'username', '123456'),
        ];

        $this->expectException(WrongDataTypePassed::class);

        $this->transactionListResponseDataAssembler->assemble($invalidData);
    }
}
