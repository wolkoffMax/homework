<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Dto;

use App\Transaction\Domain\Transaction;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

final class TransactionResponseDto
{
    /**
     * @Serializer\Type("string")
     */
    public string $id;

    /**
     * @Serializer\Type("string")
     */
    public string $type;

    /**
     * @Serializer\Type("string")
     */
    public string $amount;

    /**
     * @Serializer\Type("string")
     */
    public string $currency;

    /**
     * @Serializer\Type("string")
     */
    public string $createdAt;

    public static function createFromTransaction(Transaction $transaction): self
    {
        $dto = new self();
        $dto->id = $transaction->id();
        $dto->type = $transaction->type()->value;
        $dto->amount = $transaction->amount();
        $dto->currency = $transaction->currency();
        $dto->createdAt = $transaction->createdAt()->format(DateTimeInterface::ATOM);

        return $dto;
    }
}
