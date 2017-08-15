<?php

namespace NotificationChannels\MobilyWs\Test;

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
        $this->assertEquals('Message content is here', $message->msg);
    }
    
    /** @test */
    public function it_can_set_content()
    {
        $message = new MobilyWsMessage();
        $message->msg('Message content is here');
        
        $this->assertEquals('Message content is here', $message->msg);
    }
    
    /** @test */
    public function it_can_create_new_message()
    {
        $message = MobilyWsMessage::create('Message content is here');
        
        $this->assertEquals('Message content is here', $message->msg);
    }
    
}
