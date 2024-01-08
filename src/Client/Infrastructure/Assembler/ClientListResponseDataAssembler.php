<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Assembler;

use App\Client\Domain\Client;
use App\Client\Infrastructure\Dto\ClientResponseDto;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;

final class ClientListResponseDataAssembler
{
    public function assemble(array $clients): array
    {
        $list = [];

        /** @var Client $client */
        foreach ($clients as $client) {
            if (! $client instanceof Client) {
                throw new WrongDataTypePassed();
            }

            $list[] = ClientResponseDto::createFromClient($client);
        }

        return $list;
    }
}
