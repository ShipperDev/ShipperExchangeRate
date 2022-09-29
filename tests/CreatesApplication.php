<?php

namespace ShipperDev\ShipperExchangeRate\Tests;

use ShipperDev\ShipperExchangeRate\Providers\ShipperExchangeRateServiceProvider;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->register(ShipperExchangeRateServiceProvider::class);
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        return $app;
    }
}
