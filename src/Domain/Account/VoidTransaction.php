<?php

namespace Bank\Domain\Account;

use Bank\Domain\Money\Money;
use Carbon\Carbon;

/**
 * Null object for transactions.
 */
class VoidTransaction extends Transaction
{


    /**
     * @param string $description
     *
     * @return VoidTransaction
     * @throws \InvalidArgumentException
     */
    public static function make(string $description = 'VOID'): VoidTransaction
    {
        return new self(new Carbon(), $description, Money::fromAmount(0));
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return false;
    }
}
