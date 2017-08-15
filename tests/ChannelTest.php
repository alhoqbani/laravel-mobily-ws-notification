<?php

namespace NotificationChannels\MobilyWs\Test;

use Mockery;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotificationChannels\MobilyWs\MobilyWsApi;
use NotificationChannels\MobilyWs\MobilyWsChannel;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
use NotificationChannels\MobilyWs\MobilyWsMessage;

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

        $this->notifiable = new TestNotifiable();
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification_with_string_text()
    {
        $notificationWithText = new TestNotification('Text message as a string');
        $params = [
          'msg' => 'Text message as a string',
          'numbers' => '966550000000',
        ];

        $this->api->shouldReceive('sendString')->with($params)->andReturn(['code' => 1, 'message' => 'تمت عملية الإرسال بنجاح']);

        $response = $this->channel->send($this->notifiable, $notificationWithText);
        $this->assertEquals('تمت عملية الإرسال بنجاح', $response);
    }

    /** @test */
    public function it_can_send_a_notification_with_instance_of_MobilyWsMessage()
    {
        $messageInstance = new MobilyWsMessage('Text from message instance');
        $notificationWithMessageInstance = new TestNotification($messageInstance);

        $this->api->shouldReceive('sendMessage')
            ->with($messageInstance, $this->notifiable->mobile_number)
            ->andReturn(['code' => 1, 'message' => 'تمت عملية الإرسال بنجاح']);
        
        $response = $this->channel->send($this->notifiable, $notificationWithMessageInstance);
        $this->assertEquals('تمت عملية الإرسال بنجاح', $response);
    }

    /** @test */
    public function it_throw_an_exception_when_given_a_message_other_than_string_or_message_instance()
    {
        $notificationWithArray = new TestNotification($array = ['text message from array']);
        $params = [
            'msg' => $array,
            'numbers' => '966550000000',
        ];

        try {
            $this->channel->send($this->notifiable, $notificationWithArray);
        } catch (CouldNotSendNotification $e) {
            $this->assertContains(
                'toMobilyWs must return a string or instance of NotificationChannels\MobilyWs\MobilyWsMessage. Instance of array returned',
                $e->getMessage()
            );

            return;
        }

        $this->fail('CouldNotSendNotification exception was not raised');
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
        $this->api->shouldReceive('sendString')->with($params)->andReturn(['code' => 5, 'message' => 'كلمة المرور الخاصة بالحساب غير صحيحة']);

        try {
            $this->channel->send($this->notifiable, new TestNotification('Text message'));
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
        $this->api->shouldReceive('sendString')->with($params)->andReturn(['code' => 3, 'message' => 'رصيدك غير كافي لإتمام عملية الإرسال']);

        try {
            $this->channel->send($this->notifiable, new TestNotification('Text message'));
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
    
    /** @test */
    public function it_passes_an_instance_of_MobilyWsMessage_when_calling_toMobilyWs_method()
    {
        $notification = Mockery::mock(TestNotification::class);

        $notification->shouldReceive('toMobilyWs')
            ->with($this->notifiable, Mockery::type(MobilyWsMessage::class))
            ->andReturn(new MobilyWsMessage());
        $this->api->shouldReceive('sendMessage')->andReturn(['code' => 1, 'message' => 'ok']);
        
        $this->channel->send($this->notifiable, $notification);
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
    public $message;

    /**
     * TestNotificationWithMessageInstance constructor.
     *
     * @param $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    public function toMobilyWs($notifiable)
    {
        return $this->message;
    }
}
