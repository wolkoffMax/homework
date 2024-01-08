<?php

declare(strict_types=1);

namespace App\Client\Domain;

interface ClientRepository
{
    public function findAll(): array;

    public function add(Client $client): void;

    public function update(Client $client): void;
}
