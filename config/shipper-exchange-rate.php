<?php

return [
    'token' => env('EXCHANGE_RATES_API_TOKEN', null),

    'api_url' => env('EXCHANGE_RATES_API_URL', null),

    'pairs' => env('SHIPPER_EXCHANGE_RATE_PAIRS', 'EUR->USD,EUR->ILS,USD->ILS'),
];