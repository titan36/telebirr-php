<?php

namespace Afroeltechnologies\TelebirrLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelebirrPaymentReceived
{
    use Dispatchable, SerializesModels;

    public $paymentData;

    /**
     * Create a new event instance.
     *
     * @param array $paymentData
     */
    public function __construct(array $paymentData)
    {
        $this->paymentData = $paymentData;
    }
}