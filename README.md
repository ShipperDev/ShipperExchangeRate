# Shipper Exchange Rate

A package for getting and saving the exchange rate in the project.

## Installing

### Add package:

    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/ShipperDev/ShipperExchangeRate.git"
        },
        ...
    ],

### Install package:
    composer require shipperdev/shipper-exchange-rate

### Publish config and migration:

    php artisan vendor:publish --tag=shipper-exchange-rate

### Migrate:
    
    php artisan migrate

### .env

Require:

    EXCHANGE_RATES_API_URL="https://api.apilayer.com"
    EXCHANGE_RATES_API_TOKEN=

Optional:
    
    SHIPPER_EXCHANGE_RATE_FROM="EUR,USD"
    SHIPPER_EXCHANGE_RATE_TO="EUR,USD"
    SHIPPER_EXCHANGE_RATE_MARKUP=5 // in percent


## Usage

    use ShipperDev\ShipperExchangeRate\ShipperExchangeRate;
    
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