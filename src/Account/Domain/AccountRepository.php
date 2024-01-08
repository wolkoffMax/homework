<?php

declare(strict_types=1);

namespace App\Account\Domain;

interface AccountRepository
{
    public function findAllByClientId(string $clientId): array;

    public function findById(string $id): ?Account;

    public function add(Account $account): void;

    public function update(Account $account): void;

    public function beginTransaction(): void;

    public function rollback(): void;

    public function commit(): void;
}
