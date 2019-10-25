<?php

namespace Maksa988\WayForPay\Facades;

use Illuminate\Support\Facades\Facade;

class WayForPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'wayforpay';
    }
}
