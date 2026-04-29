<?php

use Framework\Router\Responses\CssResponse;
use Framework\Router\HandlerResponse;

class CssResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $cssResponse = new CssResponse($handler);
        $this->assertInstanceOf(CssResponse::class, $cssResponse);
    }

    public function testSend()
    {
        $cssResponse = new CssResponse();
        $cssResponse->send();
        $this->assertTrue(true); // Output method
    }
}
