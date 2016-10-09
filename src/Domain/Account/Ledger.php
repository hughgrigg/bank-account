<?php

namespace Bank\Domain\Account;

use Bank\Domain\Money\Money;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Ledger extends Collection
{
    /**
     * @return Money
     * @throws InvalidArgumentException
     */
    public function balance(): Money
    {
        return $this->balanceAt(Carbon::now());
    }

    /**
     * @param DateTime $date
     *
     * @return Money
     * @throws \InvalidArgumentException
     */
    public function balanceAt(DateTime $date): Money
    {
        return $this->filter(
            function (Transaction $transaction) use ($date) {
                return $transaction->timestamp() <= $date->getTimestamp();
            }
        )->reduce(
            function (Money $balance, Transaction $transaction) {
                return $balance->add($transaction->amount());
            },
            Money::fromAmount(0)
        );
    }

    /**
     * @param Money  $amount
     * @param string $description
     *
     * @return Transaction
     * @throws \InvalidArgumentException
     */
    public function applyTransaction(
        Money $amount,
        string $description
    ): Transaction {
        $transaction = new Transaction(
            new Carbon(),
            $description,
            $amount
        );
        $this->push($transaction);

        return $transaction;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function push($value)
    {
        if (!($value instanceof Transaction)) {
            throw new InvalidArgumentException(
                'Only transactions can be pushed to a ledger.'
            );
        }

        parent::push($value);

        return $this;
    }
}
