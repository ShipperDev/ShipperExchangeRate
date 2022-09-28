<?php

namespace EgoDigital\ShipperExchangeRate\Tests;

use EgoDigital\ShipperExchangeRate\ShipperExchangeRate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class SrcTest extends TestCase
{
    public function test_default(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @depends test_command
     * @return void
     */
    public function test_client(): void
    {
        $service = new ShipperExchangeRate();
        $rate = $service->getRate('EUR', 'USD');
        $this->assertTrue($rate > 0);
    }

    public function test_command(): void
    {
        Artisan::call('shipper-exchange-rate:fetch');
        $this->assertTrue(DB::table('shipper_exchange_rates')->count() > 0);
    }
}