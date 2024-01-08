<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

enum TransactionType: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
}
