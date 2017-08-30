<?php

namespace NotificationChannels\MobilyWs\Test;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }
    
    protected function getConfigs(array $overrides = [])
    {
        return array_merge(
            [
                'authentication'  => 'auto',
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
