<?php

namespace Bank\Domain\Overdraft;

use Bank\Domain\Money\Money;

/**
 * An overdraft with a hard limit that charges for use of the overdraft. The
 * overdraft charges are separate from the account balance.
 */
class ChargedOverdraft extends LimitedOverdraft
{
    /** @var \Bank\Domain\Money\Money */
    private $fee;

    /** @var \Bank\Domain\Money\Money */
    private $charges;

    /**
     * @param \Bank\Domain\Money\Money $limit
     * @param \Bank\Domain\Money\Money $fee
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Money $limit, Money $fee)
    {
        $this->fee = $fee;

        // Initialise charges.
        $this->charges();

        parent::__construct($limit);
    }

    /**
     * @param \Bank\Domain\Money\Money $balance
     * @param \Bank\Domain\Money\Money $withdrawal
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function acceptWithdrawal(Money $balance, Money $withdrawal): bool
    {
        $accept = parent::acceptWithdrawal($balance, $withdrawal);
        if (!$accept) {
            return $accept;
        }

        if ($withdrawal->isGreaterThan($balance)) {
            $this->charges = $this->charges->add($this->fee);
        }

        return $accept;
    }

    /**
     * @return \Bank\Domain\Money\Money
     * @throws \InvalidArgumentException
     */
    public function charges(): Money
    {
        if ($this->charges === null) {
            $this->charges = Money::fromAmount(0);
        }

        return $this->charges;
    }
}
