<?php

namespace EgoDigital\ShipperExchangeRate\Console\Commands;

use EgoDigital\ShipperExchangeRate\ShipperExchangeRate;
use Illuminate\Console\Command;

class FetchRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipper-exchange-rate:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch exchange rate by config and store into database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ShipperExchangeRate $service)
    {
        $service->fetchRates();
    }
}
