<?php

namespace Test\Unit\Domain\Overdraft;

use Bank\Domain\Account\DebitAccount;
use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\ChargedOverdraft;
use PHPUnit\Framework\TestCase;

class ChargedOverdraftTest extends TestCase
{
    /**
     * Using the overdraft should incur a charge.
     */
    public function testOverdraftCharge()
    {
        $account = new DebitAccount();
        $overdraft = new ChargedOverdraft(
            Money::fromAmount(10000),
            Money::fromAmount(500)
        );
        $account->applyOverdraft($overdraft);

        $withdrawal = $account->withdraw(Money::fromAmount(750));
        $this->assertEquals(-750, $withdrawal->amount()->amount());

        $this->assertEquals(9250, $account->availableBalance()->amount());
        $this->assertEquals(500, $overdraft->charges()->amount());
    }
}
