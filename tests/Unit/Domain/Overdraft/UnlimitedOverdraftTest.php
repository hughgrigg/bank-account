<?php

namespace Test\Unit\Domain\Overdraft;

use Bank\Domain\Account\DebitAccount;
use Bank\Domain\Money\Money;
use Bank\Domain\Overdraft\UnlimitedOverdraft;
use PHPUnit\Framework\TestCase;

class UnlimitedOverdraftTest extends TestCase
{
    /**
     * Should be able to charge anything to the overdraft.
     */
    public function testUnlimitedOverdraft()
    {
        $account = new DebitAccount();
        $account->applyOverdraft(new UnlimitedOverdraft());

        for ($i = 0; $i < 3; $i++) {
            $withdrawal = $account->withdraw(Money::fromAmount(PHP_INT_MAX));
            $this->assertEquals(
                PHP_INT_MAX * -1,
                $withdrawal->amount()->amount()
            );
        }
    }
}
