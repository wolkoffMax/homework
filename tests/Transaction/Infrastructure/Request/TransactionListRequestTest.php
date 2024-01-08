<?php

declare(strict_types=1);

namespace App\Tests\Transaction\Infrastructure\Request;

use App\Transaction\Infrastructure\Request\TransactionListRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransactionListRequestTest extends TestCase
{
    public function testConstructorSetsPropertiesCorrectly(): void
    {
        $accountId = '123e4567-e89b-12d3-a456-426614174000'; // Example UUID
        $page = 1;
        $limit = 10;

        $request = new TransactionListRequest($accountId, $page, $limit);

        $this->assertEquals($accountId, $request->accountId());
        $this->assertEquals($page, $request->page());
        $this->assertEquals($limit, $request->limit());
    }

    public function testConstructorThrowsExceptionForInvalidAccountId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TransactionListRequest('invalid-uuid', 1, 10);
    }

    public function testConstructorThrowsExceptionForInvalidPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TransactionListRequest('123e4567-e89b-12d3-a456-426614174000', 0, 10);
    }

    public function testConstructorThrowsExceptionForInvalidLimit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TransactionListRequest('123e4567-e89b-12d3-a456-426614174000', 1, 0);
    }
}
