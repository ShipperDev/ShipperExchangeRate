<?php

namespace ShipperDev\ShipperExchangeRate;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use ShipperDev\ShipperExchangeRate\Contracts\Client;
use ShipperDev\ShipperExchangeRate\Clients\ExchangeRates;
use ShipperDev\ShipperExchangeRate\Exceptions\RatePairNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;

class ShipperExchangeRate
{
    protected array $currencies;

    protected Client $client;

    protected mixed $auto_fetch_callback = null;

    /**
     *
     */
    public function __construct()
    {
        $this->client = new ExchangeRates();
        $this->mapCurrencies();
    }

    /**
     * @param callable $callback
     * @return ShipperExchangeRate
     */
    public function setAutoFetchCallback(callable $callback): self
    {
        $this->auto_fetch_callback = $callback;
        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @return float
     * @throws RatePairNotFoundException
     * @throws Exception
     */
    public function getRate(string $from, string $to): float
    {
        $rate = $this->retrieveRate($from, $to);
        if (is_null($rate)) {
            $rates = $this->fetchCurrencyRates($from, [$to]);
            $rate = collect($rates)->firstWhere('to', $to)?->rate;
            if (is_null($rate)) {
                throw new RatePairNotFoundException($from, $to);
            } else {
                $this->callAutoFetchCallback($from, $to);
            }
        }

        return  $rate;
    }

    /**
     * @param ...$args
     * @return void
     */
    protected function callAutoFetchCallback(...$args): void
    {
        if (is_callable($this->auto_fetch_callback)) {
            call_user_func($this->auto_fetch_callback, ...$args);
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @return float|null
     */
    public function retrieveRate(string $from, string $to): ?float
    {
        return DB::table('shipper_exchange_rates')->where([
            ['from', $from],
            ['to', $to],
        ])->value('rate');
    }

    /**
     * @param float $value
     * @param string $from
     * @param string $to
     * @return float
     * @throws RatePairNotFoundException
     */
    public function convert(float $value, string $from, string $to): float
    {
        if ($from == $to) {
            return $value;
        }

        return $value * $this->getRate($from, $to);
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $rate
     * @return bool
     */
    public function storeRate(string $from, string $to, float $rate): bool
    {
        return DB::table('shipper_exchange_rates')->updateOrInsert([
            'from' => $from,
            'to' => $to,
        ], [
            'rate' => $rate,
            'created_at' =>  Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * @return void
     */
    protected function mapCurrencies(): void
    {
        $this->currencies = [];
        $from = config('shipper-exchange-rate.from');
        $to = config('shipper-exchange-rate.to');
        foreach ($from as $base) {
            $this->currencies[$base] = array_diff($to, [$base]);
        }
    }

    /**
     * @throws Exception
     */
    public function fetchRates(): void
    {
        foreach ($this->currencies as $base => $currencies) {
            $this->fetchCurrencyRates($base, $currencies);
        }
    }

    /**
     * @param string $base
     * @param array $currencies
     * @return array
     * @throws Exception
     */
    public function fetchCurrencyRates(string $base, array $currencies): array
    {
        $rates = [];
        $response = $this->client->latest($base)->json();
        if ($base === $response['base']) {
            foreach ($currencies as $currency) {
                $rate = Arr::get($response['rates'], $currency);
                if ($rate) {
                    $result = $this->storeRate($base, $currency, $rate);
                    if ($result) {
                        $rates[] = (object) [
                            'from' => $base,
                            'to' => $currency,
                            'rate' => $rate,
                        ];
                    }
                }
            }
        }
        return $rates;
    }
}