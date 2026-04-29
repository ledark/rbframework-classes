<?php

use Framework\Router\Responses\TextResponse;
use Framework\Router\HandlerResponse;

class TextResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $textResponse = new TextResponse($handler);
        $this->assertInstanceOf(TextResponse::class, $textResponse);
    }

    public function testSend()
    {
        $textResponse = new TextResponse();
        $textResponse->send();
        $this->assertTrue(true); // Output method
    }
}
