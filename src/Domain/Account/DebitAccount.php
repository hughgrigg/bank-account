<?php

namespace Bank\Domain\Account;

use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\FreeOverdraft;
use Bank\Domain\Overdraft\Overdraft;
use InvalidArgumentException;

/**
 * An account with a positive / real balance.
 */
class DebitAccount implements Account
{
    /** @var Account */
    private $state;

    /** @var Ledger */
    private $ledger;

    /** @var Overdraft */
    private $overdraft;

    /**
     * Create a new account instance.
     *
     * @return Account
     */
    public function open(): Account
    {
        $this->state = null;

        return $this;
    }

    /**
     * Close the account.
     */
    public function close()
    {
        $this->state = new ClosedAccount($this);
    }

    /**
     * Get the account balance.
     *
     * @return \Bank\Domain\Money\Money
     * @throws InvalidArgumentException
     */
    public function balance(): Money
    {
        return $this->transactions()->balance();
    }

    /**
     * @return Money
     * @throws InvalidArgumentException
     */
    public function availableBalance(): Money
    {
        return $this->balance()->add($this->overdraft()->limit());
    }

    /**
     * @param Money  $amount
     * @param string $description
     *
     * @return Transaction
     * @throws InvalidArgumentException
     */
    public function deposit(
        Money $amount,
        string $description = 'deposit'
    ): Transaction {
        if ($amount->isNegative()) {
            throw new InvalidArgumentException(
                "Deposit must be positive; `{$amount->formatted()}` given."
            );
        }

        if ($this->state) {
            return $this->state->deposit($amount, $description);
        }

        return $this->transactions()->applyTransaction($amount, $description);
    }

    /**
     * @param Money  $amount
     * @param string $description
     *
     * @return Transaction
     * @throws InvalidArgumentException
     */
    public function withdraw(
        Money $amount,
        string $description = 'withdrawal'
    ): Transaction {
        if ($amount->isNegative()) {
            throw new InvalidArgumentException(
                "Withdrawal must be positive; `{$amount->formatted()}` given."
            );
        }

        if ($this->state) {
            return $this->state->withdraw($amount, $description);
        }

        $accept = $this->overdraft()->acceptWithdrawal(
            $this->transactions()->balance(),
            $amount
        );
        if ($accept) {
            return $this->transactions()->applyTransaction(
                $amount->inverse(),
                $description
            );
        }

        return VoidTransaction::make('Declined');
    }

    /**
     * @param Overdraft $overdraft
     *
     * @return void
     */
    public function applyOverdraft(Overdraft $overdraft)
    {
        if ($this->state) {
            $this->state->applyOverdraft($overdraft);

            return;
        }

        $this->overdraft = $overdraft;
    }

    /**
     * @return Ledger
     */
    public function transactions(): Ledger
    {
        if ($this->ledger === null) {
            $this->ledger = new Ledger();
        }

        return $this->ledger = $this->ledger->sortBy(
            function (Transaction $transaction) {
                return $transaction->date();
            }
        );
    }

    /**
     * @return Overdraft
     * @throws InvalidArgumentException
     */
    private function overdraft(): Overdraft
    {
        if ($this->overdraft === null) {
            // Zero overdraft if none agreed.
            $this->overdraft = new FreeOverdraft(Money::fromAmount(0));
        }

        return $this->overdraft;
    }
}
