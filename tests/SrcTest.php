<?php

namespace ShipperDev\ShipperExchangeRate\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use ShipperDev\ShipperExchangeRate\Exceptions\RatePairNotFoundException;
use ShipperDev\ShipperExchangeRate\ShipperExchangeRate;

final class SrcTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     * @throws RatePairNotFoundException
     */
    public function get_rate_methods(): void
    {
        $service = new ShipperExchangeRate();
        $service->storeRate('EUR', 'USD', 1.2);
        $rate = $service->getRate('EUR', 'USD');
        $this->assertTrue($rate > 0);
    }

    /**
     * @test
     * @return void
     * @throws RatePairNotFoundException
     */
    public function convert_method(): void
    {
        $service = new ShipperExchangeRate();
        $service->storeRate('EUR', 'USD', 1.1999999);
        $rate = $service->getRate('EUR', 'USD');
        $result = $service->convert(1, 'EUR', 'USD');
        $this->assertEquals($rate, $result);
    }

    /**
     * @test
     * @return void
     */
    public function command(): void
    {
        Artisan::call('shipper-exchange-rate:fetch');
        $this->assertEquals(4, DB::table('shipper_exchange_rates')->count());
    }

    /**
     * @test
     * @return void
     * @throws RatePairNotFoundException
     */
    public function auto_add_currency(): void
    {
        $service = new ShipperExchangeRate();
        $this->assertEquals(0, DB::table('shipper_exchange_rates')->count());
        global $test;
        $rate = $service->setAutoFetchCallback(function(...$args) {
            global $test;
            $test = $args;
        })->getRate('UAH', 'USD');
        $this->assertGreaterThan(0, $rate);
        $this->assertEquals(['UAH', 'USD'], $test);
    }

    /**
     * @test
     * @return void
     * @throws RatePairNotFoundException
     */
    public function auto_add_currency_with_markup(): void
    {
        $exchange_rate_markup = 7.25;
        config(['shipper-exchange-rate.markup_percent' => $exchange_rate_markup]);
        $service = new ShipperExchangeRate();
        $this->assertEquals(0, DB::table('shipper_exchange_rates')->count());
        $rate = $service->getRate('UAH', 'USD');
        $this->assertGreaterThan(0, $rate);
        $origin_rate = $service->retrieveRate('UAH', 'USD');
        $this->assertEquals($exchange_rate_markup, $rate / $origin_rate * 100 - 100);
    }
}