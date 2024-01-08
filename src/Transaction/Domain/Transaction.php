<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

use App\Account\Domain\Account;
use App\Shared\Domain\Service\UuidService;
use App\Shared\Domain\TimeStampableTrait;

class Transaction
{
    use TimeStampableTrait;

    private string $id;
    private string $type;
    private string $amount;
    private string $currency;
    private Account $account;

    public function __construct(TransactionType $type, Account $account, string $amount, string $currency)
    {
        $this->id = UuidService::generate();
        $this->type = $type->value;
        $this->account = $account;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): TransactionType
    {
        return TransactionType::from($this->type);
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function account(): Account
    {
        return $this->account;
    }

    public function prePersist(): void
    {
        $this->updateTimestamps();
    }
}
