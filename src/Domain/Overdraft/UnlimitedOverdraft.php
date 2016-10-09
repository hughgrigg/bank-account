<?php

namespace Bank\Domain\Overdraft;

use Bank\Domain\Money\Money;

/**
 * An overdraft for rock stars.
 */
class UnlimitedOverdraft implements Overdraft
{
    /**
     * Get the withdrawal limit.
     *
     * @return \Bank\Domain\Money\Money
     * @throws \InvalidArgumentException
     */
    public function limit(): Money
    {
        return Money::fromAmount(0);
    }

    /**
     * Determine whether a withdrawal is acceptable.
     *
     * @param \Bank\Domain\Money\Money $balance
     * @param \Bank\Domain\Money\Money $withdrawal
     *
     * @return bool
     */
    public function acceptWithdrawal(Money $balance, Money $withdrawal): bool
    {
        return true;
    }
}
