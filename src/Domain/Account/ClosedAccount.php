<?php

namespace Bank\Domain\Account;

use BadMethodCallException;
use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\Overdraft;

class ClosedAccount implements Account
{
    /** @var Account */
    private $wrapped;

    /**
     * @param Account $original
     */
    public function __construct(Account $original)
    {
        $this->wrapped = $original;
    }

    /**
     * Create a new account instance.
     *
     * @return Account
     * @throws \BadMethodCallException
     */
    public function open(): Account
    {
        return $this->wrapped->open();
    }

    /**
     * Close the account.
     *
     * @return void
     * @throws BadMethodCallException
     */
    public function close()
    {
        throw new BadMethodCallException('This account is already closed.');
    }

    /**
     * Get the account balance.
     *
     * @return \Bank\Domain\Money\Money
     */
    public function balance(): Money
    {
        return $this->wrapped->balance();
    }

    /**
     * @param Money  $amount
     * @param string $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    public function deposit(
        Money $amount,
        string $description = 'deposit'
    ): Transaction {
        return VoidTransaction::make();
    }

    /**
     * @param \Bank\Domain\Money\Money $amount
     *
     * @param string                   $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    public function withdraw(
        Money $amount,
        string $description = 'withdrawal'
    ): Transaction {
        return VoidTransaction::make();
    }

    /**
     * @param Overdraft $overdraft
     *
     * @return void
     * @throws \BadMethodCallException
     */
    public function applyOverdraft(Overdraft $overdraft)
    {
        throw new BadMethodCallException(
            'An overdraft cannot be applied to a closed account.'
        );
    }

    /**
     * @return Ledger
     */
    public function transactions(): Ledger
    {
        return $this->wrapped->transactions();
    }
}
