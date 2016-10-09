<?php

namespace Bank\Io;

use Bank\Domain\Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use NumberFormatter;

/**
 * Utility for formatting monetary amounts as an aspect. This avoids injecting
 * the behaviour into money instances, which would break their role as value
 * objects.
 */
trait FormatsMoney
{
    /** @var MoneyFormatter */
    private $moneyFormatter;

    /**
     * @param \Bank\Domain\Money\Money $money
     *
     * @return string
     */
    protected function formatMoney(Money $money): string
    {
        return $this->moneyFormatter()->format($money->wrapped());
    }

    /**
     * @return MoneyFormatter
     */
    private function moneyFormatter(): MoneyFormatter
    {
        if ($this->moneyFormatter === null) {
            $this->moneyFormatter = new IntlMoneyFormatter(
                new NumberFormatter(
                    'en_GB',
                    NumberFormatter::CURRENCY
                ),
                new ISOCurrencies()
            );
        }

        return $this->moneyFormatter;
    }
}
