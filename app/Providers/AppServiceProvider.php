<?php

namespace App\Providers;

use App\Exceptions\Exception;
use App\Services\Auth\AuthService;
use App\Services\Auth\DefaultAuthService;
use App\Services\Auth\Hasher\Hasher;
use App\Services\Auth\Hasher\MD5Hasher;
use App\Services\Auth\Session\Driver\CookieDriver;
use App\Services\Auth\Session\Driver\Driver;
use App\Services\Game\Rcon\Connector;
use App\Services\Game\Rcon\DefaultConnector;
use App\Services\Payment\Payers\Payer;
use App\Services\Payment\Payers\UnitPayPayer;
use App\Services\Payment\UnitPay\Checkout as UnitPayCheckout;
use App\Services\Payment\Payers\Pool as PayerPool;
use App\Services\Settings\DataType;
use App\Services\Settings\DefaultSettings;
use App\Services\Settings\Settings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Settings::class, DefaultSettings::class);

        $this->app->singleton(Hasher::class, config('site.auth.hasher', MD5Hasher::class));
        $this->app->singleton(Driver::class, CookieDriver::class);
        $this->app->singleton(AuthService::class, DefaultAuthService::class);

        $this->registerPayment();

        $this->app->singleton(Connector::class, DefaultConnector::class);

        $this->app->singleton(\App\Services\Voting\Tops\Pool::class, function () {
            return new \App\Services\Voting\Tops\Pool(array_map(function ($top) {
                return new $top['instance']($top);
            }, settings('tops.tops', DataType::JSON, [])));
        });
    }

    private function registerPayment(): void
    {
        $this->app->singleton(UnitPayCheckout::class, function() {
            return new UnitPayCheckout(
                config('site.payment.unitpay.id'),
                config('site.payment.unitpay.secret')
            );
        });

        $this->app->singleton(UnitPayPayer::class, function() {
            return new UnitPayPayer($this->app->make(UnitPayCheckout::class));
        });

        $this->app->singleton(PayerPool::class, function() {
            return new PayerPool(array_map(function($payer) {
                $instance = $this->app->make($payer);
                if($instance instanceof Payer) {
                    return $instance;
                }

                throw new Exception("Payer {$payer} must be implements interface App\Services\Purchasing\Payers\Payer");
            }, config('site.payment.payers')));
        });
    }
}
