<?php

declare(strict_types=1);

namespace App\Client\Application\Service;

use App\Client\Application\Exception\ClientDataFetchFailed;
use App\Client\Domain\ClientRepository;
use Throwable;

final class ClientsFetchService
{
    private ClientRepository $clients;

    public function __construct(ClientRepository $clients)
    {
        $this->clients = $clients;
    }

    public function fetch(): array
    {
        try {
            return $this->clients->findAll();
        } catch (Throwable $exception) {
            throw ClientDataFetchFailed::from($exception);
        }
    }
}
