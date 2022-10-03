<?php

namespace ShipperDev\ShipperExchangeRate;

use ShipperDev\ShipperExchangeRate\Contracts\Client;
use ShipperDev\ShipperExchangeRate\Clients\ExchangeRates;
use ShipperDev\ShipperExchangeRate\Exceptions\RatePairNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;

class ShipperExchangeRate
{
    protected array $currencies;

    protected Client $client;

    public function __construct()
    {
        $this->client = new ExchangeRates();
        $this->mapCurrencies();
    }

    /**
     * @throws \ShipperDev\ShipperExchangeRate\Exceptions\RatePairNotFoundException
     */
    public function getRate(string $from, string $to): float
    {
        $rate = DB::table('shipper_exchange_rates')->where([
            ['from', $from],
            ['to', $to],
        ])->value('rate');

        if (is_null($rate)) {
            throw new RatePairNotFoundException();
        }

        return  $rate;
    }

    /**
     * @throws \ShipperDev\ShipperExchangeRate\Exceptions\RatePairNotFoundException
     */
    public function convert(float $value, string $from, string $to): float
    {
        if ($from == $to) {
            return $value;
        }
        
        return $value * $this->getRate($from, $to);
    }

    public function storeRate(string $from, string $to, float $rate): bool
    {
        return DB::table('shipper_exchange_rates')->updateOrInsert([
            'from' => $from,
            'to' => $to,
        ], [
            'rate' => $rate,
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }

    protected function mapCurrencies(): void
    {
        $this->currencies = [];
        $pairs_str = str_replace(' ', '', config('shipper-exchange-rate.pairs'));
        foreach (explode(',', $pairs_str) as $item) {
            $pair = explode('->', $item);
            $this->currencies[$pair[0]][] = $pair[1];
        }
    }

    /**
     * @throws Exception
     */
    public function fetchRates(): void
    {
        foreach ($this->currencies as $base => $currencies) {
            $result = $this->client->latest($base)->json();
            foreach ($currencies as $currency) {
                if ($base == $result['base'] && key_exists($currency, $result['rates'])) {
                    $this->storeRate($base, $currency, $result['rates'][$currency]);
                }
            }
        }
    }
}