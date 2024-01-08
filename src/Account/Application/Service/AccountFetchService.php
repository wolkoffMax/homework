<?php

declare(strict_types=1);

namespace App\Account\Application\Service;

use App\Account\Application\Exception\AccountDataFetchFailed;
use App\Account\Domain\AccountRepository;
use Throwable;

final class AccountFetchService
{
    private AccountRepository $accounts;

    public function __construct(AccountRepository $accounts)
    {
        $this->accounts = $accounts;
    }

    public function fetch(string $clientId): array
    {
        try {
            $accounts = $this->accounts->findAllByClientId($clientId);
        } catch (Throwable $exception) {
            throw AccountDataFetchFailed::from($exception);
        }

        return $accounts;
    }
}
