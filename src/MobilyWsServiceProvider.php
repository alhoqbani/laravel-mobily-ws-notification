<?php

namespace NotificationChannels\MobilyWs;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\MobilyWs\Console\MobilyWsNotificationMakeCommand;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

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
                $mobilyWsConfig = $this->app['config']['mobilyws'];
                if (is_null($mobilyWsConfig)) {
                    throw CouldNotSendNotification::withErrorMessage('Config file was not found. Please publish the config file');
                }

                return new MobilyWsApi(
                    new MobilyWsConfig($mobilyWsConfig),
                    new Client(
                        $mobilyWsConfig['guzzle']['client']
                    )
                );
            });

        $this->publishes([
            __DIR__.'/../config/mobilyws.php' => config_path('mobilyws.php'),
        ]);
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'command.mobilyws.notification',
            function ($app) {
                return new MobilyWsNotificationMakeCommand($app['files']);
            }
        );
        $this->commands('command.mobilyws.notification');
    }
    
}
