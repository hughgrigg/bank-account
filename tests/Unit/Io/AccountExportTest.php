<?php

namespace Test\Unit\Io;

use Bank\Domain\Account\DebitAccount;
use Bank\Domain\Money\Money;
use Bank\Io\AccountExport;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AccountExportTest extends TestCase
{
    public function testAccountExport()
    {
        Carbon::setTestNow(
            Carbon::createFromFormat(
                DATE_RFC3339,
                '2017-01-01T07:08:00+00:00'
            )
        );

        $account = new DebitAccount();

        $account->deposit(Money::fromAmount(10000), 'First deposit');

        Carbon::setTestNow(Carbon::now()->addDay());
        $account->withdraw(Money::fromAmount(1500), 'Pub');

        Carbon::setTestNow(Carbon::now()->addDay());
        $account->withdraw(Money::fromAmount(2500), 'Shopping');

        $export = new AccountExport($account);

        $this->assertEquals(
            <<<CSV
2017-01-01T07:08:00+00:00,First deposit,£100.00,£100.00
2017-01-02T07:08:00+00:00,Pub,-£15.00,£85.00
2017-01-03T07:08:00+00:00,Shopping,-£25.00,£60.00
CSV
            ,
            $export->write()
        );
    }
}
