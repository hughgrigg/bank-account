<?php

namespace Bank\Io;

use Bank\Domain\Account\Account;
use Bank\Domain\Account\Transaction;

class AccountExport
{
    /** @var Account */
    private $account;

    /**
     * AccountDisplay constructor.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function write(): string
    {
        return $this->account->transactions()->map(
            function (Transaction $transaction) {
                return implode(
                    ',',
                    [
                        $transaction->date()->format(DATE_RFC3339),
                        $transaction->description(),
                        $transaction->amount()->formatted(),
                        $this->account->transactions()->balanceAt(
                            $transaction->date()
                        )->formatted(),
                    ]
                );
            }
        )->implode(PHP_EOL);
    }
}
