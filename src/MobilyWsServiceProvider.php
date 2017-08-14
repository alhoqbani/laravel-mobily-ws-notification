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
                $mobilyWsConfig = $this->app['config']['services.mobilyws'];

                return new MobilyWsApi(
                    new MobilyWsConfig($mobilyWsConfig),
                    new Client(
                        $mobilyWsConfig['guzzle']['client']
                    )
                );
            });
    
        $this->publishes([
            __DIR__. '/../config/mobilyws.php' => config_path('mobilyws.php'),
        ]);
    
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
