<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

use App\Account\Domain\Account;

final class TransactionFactory
{
    public static function create(
        TransactionType $type,
        Account $account,
        string $amount,
        string $currency
    ): Transaction {
        return new Transaction($type, $account, $amount, $currency);
    }
}
