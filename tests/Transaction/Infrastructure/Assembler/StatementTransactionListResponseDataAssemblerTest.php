<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Infrastructure\Assembler;

use App\Account\Domain\Account;
use App\Client\Domain\Client;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionType;
use App\Transaction\Infrastructure\Assembler\StatementTransactionListResponseDataAssembler;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;

class StatementTransactionListResponseDataAssemblerTest extends TestCase
{
    private StatementTransactionListResponseDataAssembler $statementTransactionListResponseDataAssembler;

    protected function setUp(): void
    {
        $this->statementTransactionListResponseDataAssembler = new StatementTransactionListResponseDataAssembler();
    }

    public function testAssemble(): void
    {
        $account = $this->createMock(Account::class);
        $account->method('id')->willReturn('18d9f700-41c2-4e9f-8af1-91a4f8031dd3');

        $transaction1 = new Transaction(TransactionType::INCOMING, $account, '10.00', 'USD');
        $transaction1->updateTimestamps();

        $transaction2 = new Transaction(TransactionType::OUTGOING, $account, '25.00', 'GBP');
        $transaction2->updateTimestamps();

        $accountId = '3e11e2d7-9564-434a-9548-e229d1e77c88';
        $year = '2024';
        $month = '12';

        $response = $this->statementTransactionListResponseDataAssembler->assemble(
            [$transaction1, $transaction2],
            $accountId,
            $year,
            $month
        );

        $this->assertSame($accountId, $response['accountId']);
        $this->assertSame($year, $response['year']);
        $this->assertSame($month, $response['month']);
        $this->assertCount(1, $response['credits']['transactions']);
        $this->assertCount(1, $response['debits']['transactions']);
        $this->assertEquals(Money::of('25.00', 'GBP'), $response['credits']['totalAmount']);
        $this->assertEquals(Money::of('10.00', 'USD'), $response['debits']['totalAmount']);
    }

    public function testThrowsAnException(): void
    {
        $invalidData = [
            new Client('Full Name', 'username', '123456'),
        ];

        $this->expectException(WrongDataTypePassed::class);

        $this->statementTransactionListResponseDataAssembler->assemble(
            $invalidData,
            '3e11e2d7-9564-434a-9548-e229d1e77c88',
            '2024',
            '12'
        );
    }
}
