# Shipper Exchange Rate

A package for getting and saving the exchange rate in the project.

## Installing

### Add package:

    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "git@github.com:ShipperDev/ShipperExchangeRate.gits"
        },
        ...
    ],

### Install package:
    composer require ego-digital/shipper-exchange-rate

### Add provider:

    \EgoDigital\ShipperExchangeRate\Providers\ShipperExchangeRateServiceProvider::class,
    to
    config/app.php file in 'providers' section

### Publish config and migration:

    php artisan vendor:publish --tag=shipper-exchange-rate

### Migrate:
    
    php artisan migrate

### .env

Require:

    EXCHANGE_RATES_API_URL=
    EXCHANGE_RATES_API_TOKEN=

Optional:
    
    SHIPPER_EXCHANGE_RATE_PAIRS="EUR->USD,EUR->ILS,USD->ILS"


## Usage

    use EgoDigital\ShipperExchangeRate\ShipperExchangeRate;
    
    ...
    
    $service = new ShipperExchangeRate();
    $rate = $service->getRate('EUR', 'USD');

*If rate not exists will be got 0.*

Or with dependency injection:

    public function index(ShipperExchangeRate $service): JsonResponse
    {
        return response()->json($service->getRate('EUR', 'USD'));
    }

### Command for sync:

    php artisan shipper-exchange-rate:fetch