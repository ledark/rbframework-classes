<?php

use Framework\Router\Responses\JsonResponse;
use Framework\Router\HandlerResponse;

class JsonResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $jsonResponse = new JsonResponse($handler);
        $this->assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testConstructorNull()
    {
        $jsonResponse = new JsonResponse();
        $this->assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testSend()
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->send();
        $this->assertTrue(true); // Output method
    }
}
