<?php

namespace Bank\Domain\Overdraft;

use Bank\Domain\Money\Money;

interface Overdraft
{
    /**
     * Get the withdrawal limit.
     *
     * @return Money
     */
    public function limit(): Money;

    /**
     * Determine whether a withdrawal is acceptable.
     *
     * @param Money $balance
     * @param Money $withdrawal
     *
     * @return bool
     */
    public function acceptWithdrawal(Money $balance, Money $withdrawal): bool;
}
