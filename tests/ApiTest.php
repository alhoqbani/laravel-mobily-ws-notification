<?php

namespace NotificationChannels\MobilyWs\Test;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use function GuzzleHttp\Psr7\parse_query;
use NotificationChannels\MobilyWs\MobilyWsApi;
use NotificationChannels\MobilyWs\MobilyWsConfig;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendMobilyWsNotification;
use NotificationChannels\MobilyWs\MobilyWsMessage;

class ApiTest extends TestCase
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
    public function it_can_send_request_to_mobily_ws_api()
    {
        $mobileWsConfig = new MobilyWsConfig($this->getConfigs());
        $mock = new MockHandler([
            new Response(200, [], 1),
        ]);
        $handler = HandlerStack::create($mock);
        $config = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler' => $handler,
        ];
        $client = new Client($config);
        $api = new MobilyWsApi($mobileWsConfig, $client);

        $params = [
            'msg' => 'SMS Text Message',
            'numbers' => '966550000000',
        ];

        $api->send($params);
    }

    /** @test */
    public function it_send_request_with_correct_params_when_given_string_message()
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], 1),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        $config = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler' => $stack,
        ];
        $client = new Client($config);
        $mobileWsConfig = new MobilyWsConfig($this->getConfigs());
        $api = new MobilyWsApi($mobileWsConfig, $client);
        $params = [
            'applicationType' => '68',
            'lang' => '3',
            'msg' => 'SMS Text Message',
            'numbers' => '966550000000',
        ];

        $api->sendString($params);

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertCount(1, $container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/api/msgSend.php', $request->getRequestTarget());
        $this->assertArraySubset($params, parse_query($request->getBody()->getContents()));
    }

    /** @test */
    public function it_send_request_with_correct_params_when_given_MobilyWsMessage_instance()
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], 1),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        $options = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler' => $stack,
        ];
        $client = new Client($options);
        $mobileWsConfig = new MobilyWsConfig($this->getConfigs());
        $api = new MobilyWsApi($mobileWsConfig, $client);

        $message = new MobilyWsMessage('SMS Text Message');
        $params = [
            'applicationType' => '68',
            'lang' => '3',
            'msg' => $message->text,
            'numbers' => $number = '966550000000',
        ];

        $api->sendMessage($message, $number);

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertCount(1, $container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/api/msgSend.php', $request->getRequestTarget());
        $this->assertArraySubset($params, parse_query($request->getBody()->getContents()));
    }
    
    /** @test */
    public function it_send_request_with_correct_credentials_when_auth_method_is_password()
    {
        $config = $this->getConfigs([
            'authentication' => 'password',
            'mobile'         => '0500000000',
            'password'       => 'anyPassword',
        ]);
    
        /** @var Request $request */
        $request = $this->sendRequest($config);

        $expectedParams = [
            'mobile'         => '0500000000',
            'password'       => 'anyPassword',
        ];
        $actualParams = parse_query($request->getBody()->getContents());
    
        $this->assertArrayNotHasKey('apiKey', $actualParams);
        $this->assertArraySubset($expectedParams, $actualParams);
    }
    
    /** @test */
    public function it_send_request_with_correct_credentials_when_auth_method_is_apiKey()
    {
        $config = $this->getConfigs([
            'authentication' => 'apiKey',
            'mobile'         => '0500000000',
            'password'       => 'anyPassword',
            'apiKey'         => 'someApiKey',
        ]);
        
        /** @var Request $request */
        $request = $this->sendRequest($config);
        $expectedParams = [
            'apiKey'         => 'someApiKey',
        ];
        $actualParams = parse_query($request->getBody()->getContents());
        
        $this->assertArrayNotHasKey('mobile', $actualParams);
        $this->assertArrayNotHasKey('password', $actualParams);
        $this->assertArraySubset($expectedParams, $actualParams);
    }
    
    /** @test */
    public function it_send_request_with_correct_credentials_when_auth_method_is_auto()
    {
        $config = $this->getConfigs([
            'authentication' => 'auto',
            'mobile'         => '0500000000',
            'password'       => 'anyPassword',
            'apiKey'         => null,
        ]);
        
        /** @var Request $request */
        $request = $this->sendRequest($config);
        $expectedParams = [
            'mobile'         => '0500000000',
            'password'       => 'anyPassword',
        ];
        $actualParams = parse_query($request->getBody()->getContents());
        
        $this->assertArrayNotHasKey('apiKey', $actualParams);
        $this->assertArraySubset($expectedParams, $actualParams);
    }

    /** @test */
    public function it_send_date_and_time_with_request_when_message_time_is_set()
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], 1),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        $options = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler' => $stack,
        ];
        $client = new Client($options);
        $mobileWsConfig = new MobilyWsConfig($this->getConfigs());
        $api = new MobilyWsApi($mobileWsConfig, $client);

        $message = new MobilyWsMessage('SMS Text Message');
        $message->time(Carbon::parse('+1 week'));
        $params = [
            'msg' => $message->text,
            'numbers' => $number = '966550000000',
            'timeSend' => $message->timeSend(),
            'dateSend' => $message->dateSend(),
        ];

        $api->sendMessage($message, $number);

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertArraySubset($params, parse_query($request->getBody()->getContents()));
    }

    /** @test */
    public function it_throw_an_exception_when_response_is_not_ok()
    {
        $mobileWsConfig = new MobilyWsConfig($this->getConfigs());
        $mock = new MockHandler([
            new Response(403, [], 1),
        ]);
        $handler = HandlerStack::create($mock);
        $config = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler' => $handler,
        ];
        $client = new Client($config);
        $api = new MobilyWsApi($mobileWsConfig, $client);

        $params = [
            'msg' => 'SMS Text Message',
            'numbers' => '966550000000',
        ];

        try {
            $api->send($params);
        } catch (CouldNotSendMobilyWsNotification $e) {
            $this->assertContains('Request to mobily.ws failed', $e->getMessage());
            $this->assertEquals('403', $e->getCode());

            return;
        }

        $this->fail('CouldNotSendMobilyWsNotification exception was not raised');
    }
    
    /**
     * Mock the request using the api and return the history container
     * @param $config
     *
     * @return array
     */
    protected function sendRequest($config)
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], 1),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        $options = [
            'base_uri' => 'http://mobily.ws/api/',
            'handler'  => $stack,
        ];
        $client = new Client($options);
        $mobileWsConfig = new MobilyWsConfig($config);
        $api = new MobilyWsApi($mobileWsConfig, $client);
        
        $message = new MobilyWsMessage('SMS Text Message');
        $api->sendMessage($message, '966550000000');
        
        return $container[0]['request'];
    }
}
