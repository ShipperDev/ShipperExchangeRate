<?php

namespace EgoDigital\ShipperExchangeRate;

use EgoDigital\ShipperExchangeRate\Contracts\Client;
use EgoDigital\ShipperExchangeRate\Clients\ExchangeRates;
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

    public function getRate(string $from, string $to): float
    {
        return DB::table('shipper_exchange_rates')->where([
                ['from', $from],
                ['to', $to],
            ])->value('rate') ?? 0;
    }

    public function convert(float $value, string $from, string $to): float
    {
        return $value * $this->getRate($from, $to);
    }

    public function storeRate(string $from, string $to, float $rate): bool
    {
        return DB::table('shipper_exchange_rates')->updateOrInsert([
            'from' => $from,
            'to' => $to,
        ], [
            'rate' => $rate,
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