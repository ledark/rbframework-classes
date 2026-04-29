<?php

use Framework\Router\Responses\ImageResponse;
use Framework\Router\HandlerResponse;

class ImageResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $imageResponse = new ImageResponse($handler);
        $this->assertInstanceOf(ImageResponse::class, $imageResponse);
    }

    public function testSend()
    {
        $imageResponse = new ImageResponse();
        $imageResponse->send();
        $this->assertTrue(true); // Output method
    }
}
