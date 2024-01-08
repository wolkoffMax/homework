<?php

namespace App\Shared\Domain;

use DateTimeImmutable;

trait TimeStampableTrait
{
    private ?DateTimeImmutable $createdAt = null;

    private ?DateTimeImmutable $updatedAt = null;

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateTimestamps(): void
    {
        $now = new DateTimeImmutable();

        $this->updatedAt = $now;

        if (null === $this->createdAt()) {
            $this->createdAt = $now;
        }
    }
}
