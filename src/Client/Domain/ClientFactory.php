<?php

declare(strict_types=1);

namespace App\Client\Domain;

final class ClientFactory
{
    public static function create(string $fullName, string $username, string $passwordHash): Client
    {
        return new Client($fullName, $username, $passwordHash);
    }
}
