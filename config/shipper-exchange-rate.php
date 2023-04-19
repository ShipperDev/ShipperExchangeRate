<?php

return [
    'token' => env('EXCHANGE_RATES_API_TOKEN', null),

    'api_url' => env('EXCHANGE_RATES_API_URL', null),

    'from' => ['EUR', 'USD', 'ILS'],

    'to' => ['EUR', 'USD'],
];