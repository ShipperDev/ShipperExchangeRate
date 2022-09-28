<?php

namespace EgoDigital\ShipperExchangeRate\Contracts;

use Illuminate\Http\Client\Response;

abstract class Client
{
    abstract public function url(): string;

    abstract public function token(): string;

    abstract public function headers(): array;

    abstract public function latest(string $base): Response;
}