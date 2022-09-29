<?php

use ShipperDev\ShipperExchangeRate\Providers\ShipperExchangeRateServiceProvider;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
$app->register(ShipperExchangeRateServiceProvider::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
Artisan::call('vendor:publish --force --tag=shipper-exchange-rate');
Artisan::call('migrate:reset');
Artisan::call('migrate');