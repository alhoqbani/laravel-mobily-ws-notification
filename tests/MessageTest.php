<?php

namespace NotificationChannels\MobilyWs\Test;

use Carbon\Carbon;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;
use NotificationChannels\MobilyWs\MobilyWsMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
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
    public function it_can_set_message_content_when_constructing()
    {
        $message = new MobilyWsMessage('Message content is here');
        
        $this->assertInstanceOf(MobilyWsMessage::class, $message);
        $this->assertEquals('Message content is here', $message->text);
    }
    
    /** @test */
    public function it_can_set_content()
    {
        $message = new MobilyWsMessage();
        $message->text('Message content is here');
        
        $this->assertEquals('Message content is here', $message->text);
    }
    
    /** @test */
    public function it_can_create_new_message()
    {
        $message = MobilyWsMessage::create('Message content is here');
        
        $this->assertEquals('Message content is here', $message->text);
    }
    
    /** @test */
    public function it_can_set_delay_from_Carbon_instance()
    {
        $carbon = Carbon::parse('+ 1 week');
        $message = MobilyWsMessage::create('Message content is here');
        $message->time($carbon);
        
        $this->assertEquals($carbon, $message->time);
        $this->assertInstanceOf(Carbon::class, $message->time);
    }
    
    /** @test */
    public function it_can_set_delay_from_DateTime_instance()
    {
        $carbon = Carbon::parse('+ 1 week');
        $dt = new \DateTime($carbon->toDateTimeString());
        $this->assertEquals($dt, $carbon);
        
        $message = MobilyWsMessage::create('Message content is here');
        $message->time($dt);

        $this->assertEquals($carbon, $message->time);
        $this->assertInstanceOf(Carbon::class, $message->time);
    }
    
    /** @test */
    public function it_can_set_delay_from_timestamp()
    {
        $carbon = Carbon::parse('+ 1 week');
        
        $message = MobilyWsMessage::create('Message content is here');
        $message->time($carbon->timestamp);

        $this->assertEquals($carbon, $message->time);
        $this->assertInstanceOf(Carbon::class, $message->time);
    }
    
    /** @test */
    public function it_throw_an_exception_when_given_invalid_time()
    {
        $message = MobilyWsMessage::create('Message content is here');
        
        try {
            $message->time(['invalid time format']);
        } catch (CouldNotSendNotification $exception) {
            $this->assertContains(
                "Time must be a timestamp or an object implementing DateTimeInterface. array is given",
                $exception->getMessage()
            );
            
            return;
        }
        
        $this->fail('An exception should be thrown when invalid time format is given');
    }
    
}
