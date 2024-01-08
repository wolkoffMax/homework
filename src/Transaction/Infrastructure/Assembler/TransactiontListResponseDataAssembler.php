<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Assembler;

use App\Shared\Infrastructure\Exception\WrongDataTypePassed;
use App\Transaction\Domain\Transaction;
use App\Transaction\Infrastructure\Dto\TransactionResponseDto;

final class TransactiontListResponseDataAssembler
{
    public function assemble(array $transactions): array
    {
        $list = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            if (! $transaction instanceof Transaction) {
                throw new WrongDataTypePassed();
            }

            $list[] = TransactionResponseDto::createFromTransaction($transaction);
        }

        return $list;
    }
}
