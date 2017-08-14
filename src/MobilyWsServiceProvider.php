<?php

namespace NotificationChannels\MobilyWs;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class MobilyWsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(MobilyWsChannel::class)
            ->needs(MobilyWsApi::class)
            ->give(function () {
                $mobilyWsConfig = config('mobilyws');

                return new MobilyWsApi(
                    new MobilyWsConfig($mobilyWsConfig),
                    new Client(
                        $mobilyWsConfig['guzzle']['client']
                    )
                );
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
