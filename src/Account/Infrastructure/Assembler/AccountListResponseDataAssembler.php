<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\Assembler;

use App\Account\Domain\Account;
use App\Account\Infrastructure\Dto\AccountResponseDto;
use App\Shared\Infrastructure\Exception\WrongDataTypePassed;

final class AccountListResponseDataAssembler
{
    public function assemble(array $accounts): array
    {
        $list = [];

        /** @var Account $account */
        foreach ($accounts as $account) {
            if (! $account instanceof Account) {
                throw new WrongDataTypePassed();
            }

            $list[] = AccountResponseDto::createFromAccount($account);
        }

        return $list;
    }
}
