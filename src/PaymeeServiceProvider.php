<?php

namespace Sindev95\Paymee;

use Illuminate\Support\ServiceProvider;

class PaymeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/paymee.php' => config_path('paymee.php')
        ],'paymee');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/paymee'),
        ]);
        $this->loadRoutesFrom(__DIR__ . "/routes/web.php");
        $this->loadViewsFrom(__DIR__ . '/views','paymee');
    }
}
