<?php

namespace Test\Unit\Domain\Account;

use Bank\Domain\Account\DebitAccount;
use Bank\Domain\Money\Money;
use PHPUnit\Framework\TestCase;

class DebitAccountTest extends TestCase
{
    /**
     * Should be able to open a new account.
     */
    public function testOpenAccount()
    {
        $account = new DebitAccount();
        $this->assertEquals(0, $account->balance()->amount());
        $this->assertTrue($account->transactions()->isEmpty());
    }

    /**
     * Should be able to close the account.
     */
    public function testCloseAccount()
    {
        $account = new DebitAccount();

        $account->close();

        $deposit = $account->deposit(Money::fromAmount(1000));
        $this->assertEquals('VOID', $deposit->description());
        $this->assertEquals(0, $deposit->amount()->amount());

        $this->assertEquals(0, $account->balance()->amount());
    }

    /**
     * Should be able to re-open the account the after closing.
     */
    public function testReOpenAccount()
    {
        $account = new DebitAccount();

        $account->close();
        $account->open();

        $deposit = $account->deposit(Money::fromAmount(1000));
        $this->assertEquals(1000, $deposit->amount()->amount());

        $this->assertEquals(1000, $account->balance()->amount());
    }

    /**
     * Should be able to make deposits.
     */
    public function testDeposit()
    {
        $account = new DebitAccount();

        $firstDeposit = $account->deposit(Money::fromAmount(1000));
        $this->assertEquals(1000, $firstDeposit->amount()->amount());

        $this->assertEquals(1000, $account->balance()->amount());

        $secondDeposit = $account->deposit(Money::fromAmount(5000));
        $this->assertEquals(5000, $secondDeposit->amount()->amount());

        $this->assertEquals(6000, $account->balance()->amount());
    }

    /**
     * Should be able to make withdrawals.
     */
    public function testWithdraw()
    {
        $account = new DebitAccount();

        $account->deposit(Money::fromAmount(1000));
        $account->withdraw(Money::fromAmount(300));

        $this->assertEquals(700, $account->balance()->amount());
    }

    /**
     * Should not be able to withdraw more than is in the account.
     */
    public function testCantWithdrawMoreThanBalance()
    {
        $account = new DebitAccount();

        $account->deposit(Money::fromAmount(1000));

        $withdrawal = $account->withdraw(Money::fromAmount(1100));

        $this->assertEquals('Declined', $withdrawal->description());
        $this->assertEquals(0, $withdrawal->amount()->amount());

        $this->assertEquals(1000, $account->balance()->amount());
    }
}
