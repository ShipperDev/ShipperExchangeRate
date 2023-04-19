<?php

return [
    'token' => env('EXCHANGE_RATES_API_TOKEN', null),

    'api_url' => env('EXCHANGE_RATES_API_URL', null),

    'from' => env('SHIPPER_EXCHANGE_RATE_FROM', ['EUR', 'USD', 'ILS']),

    'to' => env('SHIPPER_EXCHANGE_RATE_TO', ['EUR', 'USD']),
];