<?php

namespace Bank\Domain\Account;

use BadMethodCallException;
use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\Overdraft;

/**
 * An account with a negative balance and a defined credit limit which it
 * charges interest on once at the point of withdrawal.
 */
class CreditAccount extends DebitAccount
{
    /** @var Money */
    private $limit;

    /** @var int */
    private $interestRate;

    /** @var Money */
    private $interest;

    /**
     * @param \Bank\Domain\Money\Money $limit
     * @param int                      $interestRate
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Money $limit, int $interestRate)
    {
        $this->limit = $limit;
        $this->interestRate = $interestRate;

        // Initialise interest.
        $this->interest();
    }

    /**
     * @param \Bank\Domain\Money\Money $amount
     * @param string                   $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    public function deposit(
        Money $amount,
        string $description = 'deposit'
    ): Transaction {
        if ($this->availableBalance()->inverse()->add($amount)->isPositive()) {
            return VoidTransaction::make(
                'Refused: a credit account can not have a positive balance.'
            );
        }

        // Pay off interest first
        $paidOff = Money::fromAmount(
            min(
                $this->interest()->amount(),
                $amount->amount()
            )
        );
        $amount = $amount->subtract($paidOff);
        $this->interest = $this->interest->subtract($paidOff);

        return parent::deposit($amount, $description);
    }

    /**
     * @param \Bank\Domain\Money\Money $amount
     * @param string                   $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    public function withdraw(
        Money $amount,
        string $description = 'withdrawal'
    ): Transaction {
        $debit = parent::withdraw($amount, $description);
        if ($debit->isSuccessful()) {
            return $debit;
        }

        $covered = $this->balance()
            ->add($this->limit)
            ->isGreaterThanOrEqualTo($amount);
        if ($covered) {
            return $this->withdrawWithInterest($amount, $description);
        }

        return VoidTransaction::make('Declined');
    }

    /**
     * @return \Bank\Domain\Money\Money
     * @throws \InvalidArgumentException
     */
    public function availableBalance(): Money
    {
        return parent::availableBalance()
            ->add($this->limit)
            ->subtract($this->interest());
    }

    /**
     * @param Overdraft $overdraft
     *
     * @throws \BadMethodCallException
     */
    public function applyOverdraft(Overdraft $overdraft)
    {
        throw new BadMethodCallException(
            'A credit account can not have an overdraft.'
        );
    }

    /**
     * @return \Bank\Domain\Money\Money
     * @throws \InvalidArgumentException
     */
    public function interest(): Money
    {
        if ($this->interest === null) {
            $this->interest = Money::fromAmount(0);
        }

        return $this->interest;
    }

    /**
     * @param \Bank\Domain\Money\Money $amount
     * @param string                   $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    private function withdrawWithInterest(
        Money $amount,
        string $description
    ): Transaction {
        $this->interest = $this->interest()
            ->add($amount->takeInterest($this->interestRate));

        return $this->transactions()->applyTransaction(
            $amount->inverse(),
            $description
        );
    }
}
