<?php

use Framework\Router\Responses\JavascriptResponse;
use Framework\Router\HandlerResponse;

class JavascriptResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $jsResponse = new JavascriptResponse($handler);
        $this->assertInstanceOf(JavascriptResponse::class, $jsResponse);
    }

    public function testSend()
    {
        $jsResponse = new JavascriptResponse();
        $jsResponse->send();
        $this->assertTrue(true); // Output method
    }
}
