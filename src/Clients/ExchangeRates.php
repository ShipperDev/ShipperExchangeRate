<?php

namespace ShipperDev\ShipperExchangeRate\Clients;

use ShipperDev\ShipperExchangeRate\Contracts\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Exception;

class ExchangeRates extends Client
{

    /**
     * @throws Exception
     */
    public function url(): string
    {
        if ($url = config('shipper-exchange-rate.api_url')) {
            return $url;
        } else {
            throw new Exception('Please set EXCHANGE_RATES_API_URL in .env file');
        }
    }

    /**
     * @throws Exception
     */
    public function token(): string
    {
        if ($token = config('shipper-exchange-rate.token')) {
            return $token;
        } else {
            throw new Exception('Please set EXCHANGE_RATES_API_TOKEN in .env file');
        }
    }

    /**
     * @throws Exception
     */
    public function headers(): array
    {
        return [
            "Content-Type" => "text/plain",
            "apikey" => $this->token(),
        ];
    }

    /**
     * @throws Exception
     */
    public function latest(string $base): Response
    {
        return Http::withHeaders($this->headers())->get($this->url() . "/exchangerates_data/latest?base={$base}");
    }
}