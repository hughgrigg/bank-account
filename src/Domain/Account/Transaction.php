<?php

namespace Bank\Domain\Account;

use Bank\Domain\Money\Money;
use DateTime;

/**
 * Value object for an account transaction.
 */
class Transaction
{
    /** @var DateTime */
    private $date;

    /** @var string */
    private $description;

    /** @var \Bank\Domain\Money\Money */
    private $amount;

    /**
     * @param DateTime $date
     * @param string   $description
     * @param Money    $amount
     */
    public function __construct(
        DateTime $date,
        string $description,
        Money $amount
    ) {
        $this->date = $date;
        $this->description = $description;
        $this->amount = $amount;
    }

    /**
     * @return DateTime
     */
    public function date(): DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function timestamp(): int
    {
        return $this->date()->getTimestamp();
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return \Bank\Domain\Money\Money
     */
    public function amount(): Money
    {
        return $this->amount;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return true;
    }
}
