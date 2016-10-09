<?php

namespace Test\Unit\Domain\Overdraft;

use Bank\Domain\Account\DebitAccount;
use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\FreeOverdraft;
use PHPUnit\Framework\TestCase;

class FreeOverdraftTest extends TestCase
{
    /**
     * Should be able to go into overdraft.
     */
    public function testOverdraft()
    {
        $account = new DebitAccount();
        $account->applyOverdraft(new FreeOverdraft(Money::fromAmount(1000)));
        $account->deposit(Money::fromAmount(500));
        $this->assertEquals(500, $account->balance()->amount());
        $this->assertEquals(1500, $account->availableBalance()->amount());

        $withdrawal = $account->withdraw(Money::fromAmount(750));
        $this->assertEquals(-750, $withdrawal->amount()->amount());

        $this->assertEquals(-250, $account->balance()->amount());
        $this->assertEquals(750, $account->availableBalance()->amount());
    }

    /**
     * Should not be able to exceed the overdraft limit.
     */
    public function testOverdraftLimit()
    {
        $account = new DebitAccount();
        $account->applyOverdraft(new FreeOverdraft(Money::fromAmount(1000)));

        $withdrawal = $account->withdraw(Money::fromAmount(1250));
        $this->assertEquals(0, $withdrawal->amount()->amount());
        $this->assertEquals('Declined', $withdrawal->description());
        $this->assertEquals(1000, $account->availableBalance()->amount());
    }
}
