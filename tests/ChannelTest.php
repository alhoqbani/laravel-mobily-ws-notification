<?php

namespace NotificationChannels\MobilyWs\Test;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
use NotificationChannels\MobilyWs\MobilyWsApi;
use NotificationChannels\MobilyWs\MobilyWsChannel;

/**
 * @property \Mockery\MockInterface                               api
 * @property \NotificationChannels\MobilyWs\Test\TestNotification notification
 * @property \NotificationChannels\MobilyWs\MobilyWsChannel       channel
 * @property \NotificationChannels\MobilyWs\Test\TestNotifiable   notifiable
 */
class ChannelTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->api = Mockery::mock(MobilyWsApi::class);

        $this->channel = new MobilyWsChannel($this->api);

        $this->notification = new TestNotification();

        $this->notifiable = new TestNotifiable();
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $params = [
          'msg' => 'Text message',
          'numbers' => '966550000000',
        ];
        $this->api->shouldReceive('send')->with($params)->andReturn(['code' => 1, 'message' => 'تمت عملية الإرسال بنجاح']);

        $response = $this->channel->send($this->notifiable, $this->notification);
        $this->assertEquals('تمت عملية الإرسال بنجاح', $response);
    }
    
    /** @test */
    public function it_throw_an_exception_when_mobily_ws_return_an_error()
    {
        $params = [
            'msg' => 'Text message',
            'numbers' => '966550000000',
        ];
        $this->api->shouldReceive('send')->with($params)->andReturn(['code' => 3, 'message' => 'رصيدك غير كافي لإتمام عملية الإرسال']);
        
        try {
            $this->channel->send($this->notifiable, $this->notification);
        } catch (CouldNotSendNotification $e) {
            $this->assertContains('رصيدك غير كافي لإتمام عملية الإرسال', $e->getMessage());
            return;
        }
        
        $this->fail('CouldNotSendNotification exception was not raised');
    }
    
}

class TestNotifiable
{
    use Notifiable;

    public $mobile_number = '966550000000';

    public function routeNotificationForMobilyWs()
    {
        return 'mobile_number';
    }
}

class TestNotification extends Notification
{
    public function toMobilyWs($notifiable)
    {
        return 'Text message';
    }
}
