<?php

namespace NotificationChannels\MobilyWs;

use Mockery;
use ArrayAccess;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Illuminate\Contracts\Foundation\Application;

class ServiceProviderTest extends MockeryTestCase
{
    /** @var MobilyWsServiceProvider */
    protected $provider;

    /** @var App */
    protected $app;

    public function setUp()
    {
        parent::setUp();

        $this->app = Mockery::mock(App::class);
        $this->provider = new MobilyWsServiceProvider($this->app);
    }

    /** @test */
    public function it_gives_an_instantiated_mobily_ws_api_object_when_the_channel_asks_for_it()
    {
        $configArray = include __DIR__ . '/../config/mobilyws.php';

        $this->app->shouldReceive('offsetGet')
            ->with('config')
            ->andReturn([
                'services.mobilyws' => $configArray,
            ]);

        $mobilyWsApi = Mockery::mock(MobilyWsApi::class);
        $config = Mockery::mock(MobilyWsConfig::class, $configArray);

        $this->app->shouldReceive('make')->with(MobilyWsConfig::class)->andReturn($config);
        $this->app->shouldReceive('make')->with(MobilyWsApi::class)->andReturn($mobilyWsApi);

        $this->app->shouldReceive('when')->with(MobilyWsChannel::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('needs')->with(MobilyWsApi::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('give')->with(Mockery::on(function ($mobilyWsApi) {
            return $mobilyWsApi() instanceof MobilyWsApi;
        }))->once();

        $this->provider->boot();
    }
}

interface App extends Application, ArrayAccess
{
}

function config_path()
{

}