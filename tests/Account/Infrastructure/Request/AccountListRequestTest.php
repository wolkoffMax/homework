<?php

declare(strict_types=1);

namespace Account\Infrastructure\Request;

use App\Account\Infrastructure\Request\AccountListRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AccountListRequestTest extends TestCase
{
    public function testConstructorAndGetClientId(): void
    {
        $clientId = '123e4567-e89b-12d3-a456-426614174000'; // Example UUID
        $request = new AccountListRequest($clientId);

        $this->assertEquals($clientId, $request->clientId());
    }

    public function testConstructorThrowsExceptionForInvalidClientId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AccountListRequest('invalid-uuid');
    }
}
