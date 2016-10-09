<?php

namespace Bank\Domain\Account;

use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\Overdraft;

interface Account
{
    /**
     * Open the account.
     *
     * @return Account
     */
    public function open(): Account;

    /**
     * Close the account.
     *
     * @return Account
     */
    public function close();

    /**
     * Get the account balance.
     *
     * @return Money
     */
    public function balance(): Money;

    /**
     * @param Money  $amount
     * @param string $description
     *
     * @return Transaction
     */
    public function deposit(
        Money $amount,
        string $description = 'deposit'
    ): Transaction;

    /**
     * @param \Bank\Domain\Money\Money $amount
     *
     * @param string                   $description
     *
     * @return Transaction
     */
    public function withdraw(
        Money $amount,
        string $description = 'withdrawal'
    ): Transaction;

    /**
     * @param Overdraft $overdraft
     *
     * @return void
     */
    public function applyOverdraft(Overdraft $overdraft);

    /**
     * @return Ledger
     */
    public function transactions(): Ledger;
}
