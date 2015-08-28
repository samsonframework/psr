<?php
namespace tests;
use samsonframework\psr\Response;

/**
 * Created by Nikita Kotenko <kotenko@samsonos.com>
 * on 28.08.15 at 16:42
 */
class RsponseTest extends \PHPUnit_Framework_TestCase {
	/** @var \samsonframework\psr\Message */
	protected $response;

	/** Tests init */
	public function setUp() {
		// Create response instance
		$this->response = new \samsonframework\psr\Response();
	}

	public function testStatusCode() {
		// Check original getStatusCode statuse code
		$this->assertEquals( 200, $this->response->getStatusCode() );
	}

	public function testStatusChange() {
		// Create new response with new statuce code
		$newResponse = $this->response->withStatus( 404 );

		// Check new response status code
		$this->assertEquals( 404, $newResponse->getStatusCode() );

		// Check new response reason phrase
		$this->assertEquals( 'Not Found', $newResponse->getReasonPhrase() );
	}
}
