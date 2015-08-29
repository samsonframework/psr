<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 27.08.2015
 * Time: 16:37
 */
namespace tests;


class UriTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonframework\psr\Uri */
    protected $uri;

    /** Tests init */
    public function setUp()
    {
        // Create message instance
        $this->uri = new \samsonframework\psr\Uri();
    }

    public function testGetScheme()
    {
        // Check original message protocol version
        $this->assertEquals('http', $this->uri->getScheme());
    }

    public function testGetAuthority()
    {

    }
}
