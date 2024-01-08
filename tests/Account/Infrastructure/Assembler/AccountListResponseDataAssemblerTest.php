<?php

declare(strict_types=1);

namespace App\Tests\Account\Infrastructure\Assembler;

use App\Account\Domain\Account;
use App\Account\Infrastructure\Assembler\AccountListResponseDataAssembler;
use App\Account\Infrastructure\Dto\AccountResponseDto;
use App\Client\Domain\Client;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use PHPUnit\Framework\TestCase;

class AccountListResponseDataAssemblerTest extends TestCase
{
    private AccountListResponseDataAssembler $accountListResponseDataAssembler;

    protected function setUp(): void
    {
        $this->accountListResponseDataAssembler = new AccountListResponseDataAssembler();
    }

    public function testAssemblesSuccessfully(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('id')->willReturn('18d9f700-41c2-4e9f-8af1-91a4f8031dd3');

        $account1 = new Account($client, 'USD', '100.00');
        $account1->updateTimestamps();

        $account2 = new Account($client, 'GBP', '2500.00');
        $account2->updateTimestamps();

        $accounts = [$account1, $account2];

        $result = $this->accountListResponseDataAssembler->assemble($accounts);

        $this->assertCount(2, $result);

        foreach ($result as $item) {
            $this->assertInstanceOf(AccountResponseDto::class, $item);
        }
    }

    public function testThrowsAnException(): void
    {
        $invalidData = [
            new Client('Full Name', 'username', '123456'),
        ];

        $this->expectException(WrongDataTypePassed::class);

        $this->accountListResponseDataAssembler->assemble($invalidData);
    }
}
