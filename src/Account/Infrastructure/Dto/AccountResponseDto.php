<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Dto;

use App\Account\Domain\Account;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

final class AccountResponseDto
{
    /**
     * @Serializer\Type("string")
     */
    public string $id;

    /**
     * @Serializer\Type("string")
     */
    public string $currency;

    /**
     * @Serializer\Type("string")
     */
    public string $balance;

    /**
     * @Serializer\Type("string")
     */
    public string $createdAt;

    /**
     * @Serializer\Type("string")
     */
    public string $updatedAt;

    public static function createFromAccount(Account $account): self
    {
        $dto = new self();
        $dto->id = $account->id();
        $dto->currency = $account->currency();
        $dto->balance = $account->balance();
        $dto->createdAt = $account->createdAt()->format(DateTimeInterface::ATOM);
        $dto->updatedAt = $account->updatedAt()->format(DateTimeInterface::ATOM);

        return $dto;
    }
}
