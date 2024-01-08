<?php

declare(strict_types=1);

namespace App\Account\Domain;

use App\Client\Domain\Client;
use App\Shared\Domain\Service\UuidService;
use App\Shared\Domain\TimeStampableTrait;

class Account
{
    use TimeStampableTrait;

    private string $id;
    private string $currency;
    private string $balance;
    private Client $client;

    public function __construct(Client $client, string $currency, string $balance)
    {
        $this->id = UuidService::generate();
        $this->client = $client;
        $this->currency = $currency;
        $this->balance = $balance;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function balance(): string
    {
        return $this->balance;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function setBalance(string $balance): void
    {
        $this->balance = $balance;
    }

    public function prePersist(): void
    {
        $this->updateTimestamps();
    }
}
