<?php

namespace Test\Unit\Domain\Account;

use Bank\Domain\Account\CreditAccount;
use Bank\Domain\Money\Money;
use PHPUnit\Framework\TestCase;

class CreditAccountTest extends TestCase
{
    /**
     * Should be able to go into credit.
     */
    public function testCredit()
    {
        $account = new CreditAccount(Money::fromAmount(1000), 5);

        $withdrawal = $account->withdraw(Money::fromAmount(300));
        $this->assertEquals(-300, $withdrawal->amount()->amount());

        $this->assertEquals(-300, $account->balance()->amount());
    }

    /**
     * Interest should be charged on a credit transaction.
     */
    public function testInterest()
    {
        $account = new CreditAccount(Money::fromAmount(1000), 25);

        $withdrawal = $account->withdraw(Money::fromAmount(300));
        $this->assertEquals(-300, $withdrawal->amount()->amount());

        $this->assertEquals(75, $account->interest()->amount());
    }

    /**
     * Available balance should consider interest.
     */
    public function testAvailableBalance()
    {
        $account = new CreditAccount(Money::fromAmount(1000), 25);

        $account->withdraw(Money::fromAmount(300));

        $this->assertEquals(
            1000 - (300 + (300 * 0.25)),
            $account->availableBalance()->amount()
        );
    }

    /**
     * Deposits should go to paying off interest first.
     */
    public function testPartialInterestRepayment()
    {
        $account = new CreditAccount(Money::fromAmount(1000), 25);

        $account->withdraw(Money::fromAmount(300));
        $this->assertEquals(-300, $account->balance()->amount());
        $this->assertEquals(75, $account->interest()->amount());

        $account->deposit(Money::fromAmount(50));

        $this->assertEquals(25, $account->interest()->amount());
        $this->assertEquals(-300, $account->balance()->amount());
    }

    /**
     * Deposits should pay off interest first and then be added to the account
     * balance.
     */
    public function testFullInterestRepayment()
    {
        $account = new CreditAccount(Money::fromAmount(1000), 25);

        $account->withdraw(Money::fromAmount(300));
        $this->assertEquals(-300, $account->balance()->amount());
        $this->assertEquals(75, $account->interest()->amount());

        $account->deposit(Money::fromAmount(100));

        $this->assertEquals(0, $account->interest()->amount());
        $this->assertEquals(-275, $account->balance()->amount());
    }
}
