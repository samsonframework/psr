<?php
namespace tests;
use samsonframework\psr\Stream;

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
        // Check original message protocol version
        $this->assertEquals('1.0', $this->message->getProtocolVersion());
    }

    public function testProtocolVersionChange()
    {
        // Create new message with new protocol version
        $newMessage = $this->message->withProtocolVersion('1.1');

        // Check new message protocol version
        $this->assertEquals('1.1', $newMessage->getProtocolVersion());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithHeader()
    {
        // Create new message with new header
        $newMessage = $this->message->withHeader('Content-type', 'text/plain');
        $newMessage2 = $newMessage->withHeader('Content-type', 'TEXT/PLAIN');

        $this->assertEquals(array('text/plain'), $newMessage->getHeader('content-type'));
        $this->assertEquals(array('TEXT/PLAIN'), $newMessage2->getHeader('content-type'));

        // Invalid header value
        $newMessage3 = $newMessage2->withAddedHeader('Content-Encoding', 'gzip'."\r");
    }

    public function testGetHeader()
    {
        // Create new message with new header
        $newMessage = $this->message->withHeader('Content-type', 'text/plain');
        $this->assertEquals(array('text/plain'), $newMessage->getHeader('content-type'));
        $this->assertEquals(array('text/plain'), $newMessage->getHeader('cOntent-tYpe'));
    }

    public function testHeaderGetLine()
    {
        // Create new message with new header
        $newMessage = $this->message->withHeader('Content-type', array('text/plain', 'json/application'));
        $this->assertEquals('text/plain,json/application', $newMessage->getHeaderLine('content-type'));
        $this->assertEquals('text/plain,json/application', $newMessage->getHeaderLine('cOntent-Type'));
    }

    public function testHasHeader()
    {
        // Create new message with new header
        $newMessage = $this->message->withHeader('Content-type', 'text/plain');
        $this->assertEquals(true, $newMessage->hasHeader('content-type'));
        $this->assertEquals(false, $newMessage->hasHeader('set-cookie'));
    }

    public function testGetHeaders()
    {
        // Create new message with new header
        $newMessage = $this->message->withHeader('Content-type', 'text/plain');
        $newMessage = $newMessage->withHeader('content-Encoding', 'gzip');

        $headers = $newMessage->getHeaders();
        $this->assertArrayHasKey('content-Encoding', $headers);
        $this->assertArrayHasKey('Content-type', $headers);
        $this->assertArrayNotHasKey('Content-Encoding', $headers);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithAddedHeader()
    {
        // Create new message with new header
        $newMessage = $this->message->withAddedHeader('Content-type', 'text/plain');
        $newMessage2 = $newMessage->withAddedHeader('Content-type', 'XML/PLAIN');
        $newMessage3 = $newMessage2->withAddedHeader('Content-Encoding', 'gzip');

        $this->assertEquals(array('text/plain'), $newMessage->getHeader('content-type'));
        $this->assertEquals(array('text/plain', 'XML/PLAIN'), $newMessage2->getHeader('content-type'));
        $this->assertEquals(array('gzip'), $newMessage3->getHeader('content-encoding'));

        // Invalid header value
        $newMessage4 = $newMessage3->withAddedHeader('Content-Encoding', 'gzip'."\r");
    }


    public function testWithoutHeader()
    {
        // Create new message with new header
        $newMessage = $this->message->withAddedHeader('Content-type', 'text/plain');
        $newMessage2 = $newMessage->withoutHeader('Content-type');

        $this->assertEquals(array('text/plain'), $newMessage->getHeader('content-type'));
        $this->assertEquals(array(), $newMessage2->getHeader('content-type'));
    }

    public function testWithBody()
    {
        $this->tmpnam = tempnam(sys_get_temp_dir(), 'psr');
        file_put_contents($this->tmpnam, 'FOO BAR');
        $resource = fopen($this->tmpnam, 'wb+');
        $body = new Stream($resource);

        // Create new message with new header
        $newMessage = $this->message->withBody($body);

        $this->assertEquals($body, $newMessage->getBody());
    }
}
