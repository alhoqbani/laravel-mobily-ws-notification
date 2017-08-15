<?php

namespace NotificationChannels\MobilyWs\Test;

use Mockery;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\MobilyWs\MobilyWsApi;
use NotificationChannels\MobilyWs\MobilyWsChannel;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

/**
 * @property \Mockery\MockInterface                               api
 * @property \NotificationChannels\MobilyWs\Test\TestNotification notification
 * @property \NotificationChannels\MobilyWs\MobilyWsChannel       channel
 * @property \NotificationChannels\MobilyWs\Test\TestNotifiable   notifiable
 * @property \Mockery\MockInterface                               events
 */
class ChannelTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->api = Mockery::mock(MobilyWsApi::class);

        $this->events = Mockery::spy(Dispatcher::class);

        $this->channel = new MobilyWsChannel($this->api, $this->events);

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

    /** @test
     * @expectedException \NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
     */
    public function it_fires_failure_event_on_failure()
    {
        $params = [
            'msg' => 'Text message',
            'numbers' => '966550000000',
        ];
        $this->api->shouldReceive('send')->with($params)->andReturn(['code' => 5, 'message' => 'كلمة المرور الخاصة بالحساب غير صحيحة']);

        try {
            $this->channel->send($this->notifiable, $this->notification);
        } catch (CouldNotSendNotification $e) {
            $this->events->shouldHaveReceived('fire');
        }
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

    /** @test */
    public function it_throw_an_exception_when_toMobilyWs_method_does_not_exist()
    {
        try {
            $this->channel->send($this->notifiable, new Notification());
        } catch (CouldNotSendNotification $e) {
            $this->assertContains('MobilyWs notifications must have toMobilyWs method', $e->getMessage());

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
        return $this->mobile_number;
    }
}

class TestNotification extends Notification
{
    public function toMobilyWs($notifiable)
    {
        return 'Text message';
    }
}
