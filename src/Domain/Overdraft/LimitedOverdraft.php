<?php

namespace Bank\Domain\Overdraft;

use Bank\Domain\Money\Money;

/**
 * An overdraft with a limit.
 */
abstract class LimitedOverdraft implements Overdraft
{
    /** @var Money */
    private $limit;

    /**
     * @param \Bank\Domain\Money\Money $limit
     */
    public function __construct(Money $limit)
    {
        $this->limit = $limit;
    }

    /**
     * Get the withdrawal limit.
     *
     * @return Money
     */
    public function limit(): Money
    {
        return $this->limit;
    }

    /**
     * Determine whether a withdrawal is acceptable.
     *
     * @param Money                    $balance
     * @param \Bank\Domain\Money\Money $withdrawal
     *
     * @return bool
     */
    public function acceptWithdrawal(Money $balance, Money $withdrawal): bool
    {
        return $balance->add($this->limit)->isGreaterThan($withdrawal);
    }
}
