<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 27.08.2015
 * Time: 15:23
 */
namespace tests;


class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonframework\psr\Request */
    protected $request;

    /** Tests init */
    public function setUp()
    {
        // Create message instance
        $this->request = new \samsonframework\psr\Request();
    }

    public function testGetRequestTarget()
    {
        // Check original message protocol version
        $this->assertEquals('/', $this->request->getRequestTarget());
    }
}
