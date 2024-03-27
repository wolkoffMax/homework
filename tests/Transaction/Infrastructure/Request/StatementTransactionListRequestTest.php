<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Infrastructure\Request;

use App\Transaction\Infrastructure\Request\StatementTransactionListRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StatementTransactionListRequestTest extends TestCase
{
    public function testConstructorSetsPropertiesCorrectly(): void
    {
        $accountId = '123e4567-e89b-12d3-a456-426614174000';
        $year = '2024';
        $month = '12';

        $request = new StatementTransactionListRequest($accountId, $year, $month);

        $this->assertEquals($accountId, $request->accountId());
        $this->assertEquals($year, $request->year());
        $this->assertEquals($month, $request->month());
    }

    public function testConstructorThrowsExceptionForInvalidAccountId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StatementTransactionListRequest('invalid-uuid', '2024', '12');
    }

    public function testConstructorThrowsExceptionForInvalidYear(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StatementTransactionListRequest('123e4567-e89b-12d3-a456-426614174000', 'wrong value', '12');
    }

    public function testConstructorThrowsExceptionForInvalidMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StatementTransactionListRequest('123e4567-e89b-12d3-a456-426614174000', '2024', 'wrong value');
    }
}
