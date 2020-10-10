<?php

namespace Maksa988\WayForPay;

use Illuminate\Support\ServiceProvider;

class WayForPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/wayforpay.php' => config_path('wayforpay.php'),
        ], 'config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wayforpay.php', 'wayforpay');

        $this->app->singleton('wayforpay', function () {
            return $this->app->make(WayForPay::class);
        });

        $this->app->alias('wayforpay', 'WayForPay');
    }
}
