<?php

namespace ShipperDev\ShipperExchangeRate\Providers;

use ShipperDev\ShipperExchangeRate\Console\Commands\FetchRatesCommand;
use Illuminate\Support\ServiceProvider;

class ShipperExchangeRateServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/shipper-exchange-rate.php' => config_path('shipper-exchange-rate.php'),
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'shipper-exchange-rate');
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchRatesCommand::class,
            ]);
        }
    }
}
