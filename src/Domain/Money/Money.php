<?php

namespace Bank\Domain\Money;

use Bank\Io\FormatsMoney;
use Money\Currency;

class Money
{
    use FormatsMoney;

    /** @var \Money\Money */
    private $money;

    /**
     * Money constructor.
     *
     * @param \Money\Money $money
     */
    public function __construct(\Money\Money $money)
    {
        $this->money = $money;
    }

    /**
     * @param int    $amount
     * @param string $currency
     *
     * @return Money
     * @throws \InvalidArgumentException
     */
    public static function fromAmount(int $amount, string $currency = 'GBP')
    {
        return new self(new \Money\Money($amount, new Currency($currency)));
    }

    /**
     * @param Money $money
     *
     * @return Money
     */
    public function add(Money $money): Money
    {
        return new self($this->money->add($money->wrapped()));
    }

    /**
     * @param Money $money
     *
     * @return Money
     */
    public function subtract(Money $money): Money
    {
        return new self($this->money->subtract($money->wrapped()));
    }

    /**
     * @param Money $money
     *
     * @return bool
     */
    public function isGreaterThan(Money $money): bool
    {
        return $this->money->greaterThan($money->wrapped());
    }

    /**
     * @param Money $money
     *
     * @return bool
     */
    public function isGreaterThanOrEqualTo(Money $money): bool
    {
        return $this->money->greaterThanOrEqual($money->wrapped());
    }

    /**
     * @return bool
     */
    public function isPositive(): bool
    {
        return $this->money->isPositive();
    }

    public function isNegative(): bool
    {
        return $this->money->isNegative();
    }

    /**
     * @return int
     */
    public function amount(): int
    {
        return $this->money->getAmount();
    }

    /**
     * @return Money
     * @throws \InvalidArgumentException
     */
    public function inverse(): Money
    {
        return self::fromAmount(
            $this->amount() * -1,
            $this->money->getCurrency()->getCode()
        );
    }

    /**
     * @param int $rate
     *
     * @return Money
     * @throws \InvalidArgumentException
     */
    public function takeInterest(int $rate): Money
    {
        return self::fromAmount(
            (int) ($this->amount() * ($rate / 100)),
            $this->money->getCurrency()->getCode()
        );
    }

    /**
     * @return \Money\Money
     */
    public function wrapped(): \Money\Money
    {
        return $this->money;
    }

    /**
     * @return string
     */
    public function formatted(): string
    {
        return $this->formatMoney($this);
    }
}
