<?php

namespace NotificationChannels\MobilyWs\Test;

use NotificationChannels\MobilyWs\MobilyWsConfig;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /** @test */
    public function it_return_the_correct_config_value()
    {
        $configArray = include __DIR__.'/../config/mobilyws.php';
        $config = array_merge($configArray, [
            'lang' => '4',
        ]);
        $mobilyWsConfig = new MobilyWsConfig($config);
        $guzzle = [
            'client' => [
                'base_uri' => 'http://mobily.ws/api/',
            ],
            'request' => [
                'http_errors' => true,
                'debug' => false,
            ],
        ];

        $this->assertEquals('4', $mobilyWsConfig->lang);
        $this->assertSame($guzzle, $mobilyWsConfig->guzzle);
        $this->assertNull($mobilyWsConfig->propertyDoesNotExist);
    }

    /** @test */
    public function it_return_the_request_options()
    {
        $configArray = include __DIR__.'/../config/mobilyws.php';
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $requestOptions = [
                'http_errors' => true,
                'debug' => false,
        ];

        $this->assertEquals($requestOptions, $mobilyWsConfig->request);
    }
}
