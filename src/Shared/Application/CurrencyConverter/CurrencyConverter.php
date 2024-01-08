<?php

declare(strict_types=1);

namespace App\Shared\Application\CurrencyConverter;

use Brick\Money\Currency;
use Brick\Money\Money;

interface CurrencyConverter
{
    public function convert(Money $amount, Currency $targetCurrency): Money;
}
