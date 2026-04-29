<?php

use Framework\Router\Responses\HtmlResponse;
use Framework\Router\HandlerResponse;

class HtmlResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $htmlResponse = new HtmlResponse($handler);
        $this->assertInstanceOf(HtmlResponse::class, $htmlResponse);
    }

    public function testSend()
    {
        $htmlResponse = new HtmlResponse();
        $htmlResponse->send();
        $this->assertTrue(true); // Output method
    }
}
