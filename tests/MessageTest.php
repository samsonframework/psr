<?php
namespace tests;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonframework\psr\Message */
    protected $message;

    /** Tests init */
    public function setUp()
    {
        // Create message instance
        $this->message = new \samsonframework\psr\Message();
    }

    public function testProtocolVersion()
    {
        // Create new message with new protocol version
        $newMessage = $this->message->withProtocolVersion('1.1');

        // Check new message protocol version
        $this->assertEquals('1.1', $newMessage->getProtocolVersion());
        // Check original message protocol version
        $this->assertEquals('1.0', $this->message->getProtocolVersion());
    }
}
