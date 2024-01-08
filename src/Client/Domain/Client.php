<?php

declare(strict_types=1);

namespace App\Client\Domain;

use App\Shared\Domain\Service\UuidService;
use App\Shared\Domain\TimeStampableTrait;

class Client
{
    use TimeStampableTrait;

    private string $id;
    private string $fullName;
    private string $username;
    private string $passwordHash;

    public function __construct(string $fullName, string $username, string $passwordHash)
    {
        $this->id = UuidService::generate();
        $this->fullName = $fullName;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function prePersist(): void
    {
        $this->updateTimestamps();
    }
}
