<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Dto;

use App\Client\Domain\Client;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

final class ClientResponseDto
{
    /**
     * @Serializer\Type("string")
     */
    public string $id;

    /**
     * @Serializer\Type("string")
     */
    public string $fullName;

    /**
     * @Serializer\Type("string")
     */
    public string $username;

    /**
     * @Serializer\Type("string")
     */
    public string $createdAt;

    /**
     * @Serializer\Type("string")
     */
    public string $updatedAt;

    public static function createFromClient(Client $client): self
    {
        $dto = new self();
        $dto->id = $client->id();
        $dto->fullName = $client->fullName();
        $dto->username = $client->username();
        $dto->createdAt = $client->createdAt()->format(DateTimeInterface::ATOM);
        $dto->updatedAt = $client->updatedAt()->format(DateTimeInterface::ATOM);

        return $dto;
    }
}
