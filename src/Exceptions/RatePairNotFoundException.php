<?php

namespace ShipperDev\ShipperExchangeRate\Exceptions;

class RatePairNotFoundException extends \Exception
{
    public string $from;
    public string $to;

    public function __construct(
        string $from = "",
        string $to = "",
        ?Throwable $previous = null
    ) {
        $this->from = $from;
        $this->to   = $to;
        $message = __("Rate pair :FROM => :TO not found.", [
            'from' => $from,
            'to'   => $to
        ]);
        parent::__construct($message, $code = 500, $previous);
    }
}