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
        $configArray = $this->getConfigs();
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
        $configArray = $this->getConfigs();
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $requestOptions = [
                'http_errors' => true,
                'debug' => false,
        ];

        $this->assertEquals($requestOptions, $mobilyWsConfig->request);
    }
    private function getConfigs(array $overrides = [])
    {
        return array_merge(
            [
                'mobile'          => '96650000',
                'password'        => '123',
                'sender'          => 'sender',
                'applicationType' => 68,
                'lang'            => '3',
                'guzzle'          => [
                    'client'  => [
                        'base_uri' => 'http://mobily.ws/api/',
                    ],
                    'request' => [
                        'http_errors' => true,
                        'debug'       => false,
                    ],
                ],
            ],
            $overrides
        );
    }
}
