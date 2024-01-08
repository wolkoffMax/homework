<?php

declare(strict_types=1);

namespace App\Tests\Client\Infrastructure\Assembler;

use App\Client\Domain\Client;
use App\Client\Infrastructure\Assembler\ClientListResponseDataAssembler;
use App\Client\Infrastructure\Dto\ClientResponseDto;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ClientListResponseDataAssemblerTest extends TestCase
{
    private ClientListResponseDataAssembler $assembler;

    protected function setUp(): void
    {
        $this->assembler = new ClientListResponseDataAssembler();
    }

    public function testAssemble(): void
    {
        $client = new Client('Full Name', 'username', '123456');
        $client->updateTimestamps();

        $clients = [$client];

        $result = $this->assembler->assemble($clients);

        $this->assertIsArray($result);
        $this->assertInstanceOf(ClientResponseDto::class, $result[0]);
    }

    public function testAssembleThrowsExceptionWithWrongDataType(): void
    {
        $this->expectException(WrongDataTypePassed::class);

        $wrongType = new stdClass();
        $clients = [$wrongType];

        $this->assembler->assemble($clients);
    }
}
