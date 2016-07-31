<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 01.08.16 at 01:05
 */
namespace samsonframework\psr;

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    public function testClasses()
    {
        $request = new Request();
        $responce = new Response();
        $uri = new Uri();

        $stream = new Stream('php://input');
        $errorStatus = false;
        $uploadedFile = new UploadedFile($stream, 1000, UPLOAD_ERR_OK);
        $serverRequest = new ServerRequest();

        $this->assertEquals(true, true);
    }
}