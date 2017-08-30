<?php

namespace NotificationChannels\MobilyWs\Test;

use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
use NotificationChannels\MobilyWs\MobilyWsConfig;

class ConfigTest extends TestCase
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
    public function it_return_the_correct_authentication_method()
    {
        $configArray = $this->getConfigs([]);
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $this->assertEquals('auto', $mobilyWsConfig->getAuthenticationMethod());
        
        $configArray = $this->getConfigs(['authentication' => 'apiKey']);
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $this->assertEquals('apiKey', $mobilyWsConfig->getAuthenticationMethod());
        
        $configArray = $this->getConfigs(['authentication' => 'password']);
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $this->assertEquals('password', $mobilyWsConfig->getAuthenticationMethod());
    }
    
    /** @test */
    public function it_throws_an_exception_when_authentication_method_is_not_set()
    {
        try {
            $configArray = $this->getConfigs(['authentication' => null]);
            $mobilyWsConfig = new MobilyWsConfig($configArray);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals(
              $e->getMessage(),
              "Please set the authentication method in the mobilyws config file"
            );
            return;
        }
        
        $this->fail('No exception was thrown when the authentication method was not set');
    }
    
    /** @test */
    public function it_return_the_correct_authentication_credentials_when_auth_mode_is_password()
    {
        $configArray = $this->getConfigs([
            'authentication' => 'password',
            'mobile' => '05000000000',
            'password' => 'somePassword',
            'apiKey' => 'anyKey'
        ]);
        
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $expectedCredentials = [
            'mobile' => '05000000000',
            'password' => 'somePassword',
        ];
        $this->assertSame($expectedCredentials, $mobilyWsConfig->getCredentials());
    }
    
    /** @test */
    public function it_return_the_correct_authentication_credentials_when_auth_mode_is_api()
    {
        $configArray = $this->getConfigs([
            'authentication' => 'apiKey',
            'mobile' => '05000000000',
            'password' => 'somePassword',
            'apiKey' => 'anyKey'
        ]);
        
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $expectedCredentials = [
            'apiKey' => 'anyKey',
        ];
        $this->assertSame($expectedCredentials, $mobilyWsConfig->getCredentials());
    }
    
    /** @test */
    public function it_return_the_correct_authentication_credentials_when_auth_mode_is_auto()
    {
        $configArray = $this->getConfigs([
            'authentication' => 'auto',
            'mobile' => '05000000000',
            'password' => 'somePassword',
            'apiKey' => null
        ]);
        
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $expectedCredentials = [
            'mobile' => '05000000000',
            'password' => 'somePassword',
        ];
        $this->assertSame($expectedCredentials, $mobilyWsConfig->getCredentials());
        
        $configArray = $this->getConfigs([
            'authentication' => 'auto',
            'mobile' => '05000000000',
            'password' => 'somePassword',
            'apiKey' => 'withApiKey'
        ]);
        
        $mobilyWsConfig = new MobilyWsConfig($configArray);
        $expectedCredentials = [
            'apiKey' => 'withApiKey'
        ];
        $this->assertSame($expectedCredentials, $mobilyWsConfig->getCredentials());
    }
    
    /** @test */
    public function it_throws_an_exception_when_the_authentication_method_is_not_supported()
    {
        try {
            $configArray = $this->getConfigs(['authentication' => 'unsupportedMethod']);
            $mobilyWsConfig = new MobilyWsConfig($configArray);
        } catch (CouldNotSendNotification $e) {
            $this->assertEquals(
                $e->getMessage(),
                "Method unsupportedMethod is not supported. Please choose from: (apiKey, password, auto)"
            );
            
            return;
        }
        
        $this->fail('No exception was thrown when the provided authentication is not supported');
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

}
