<?php

use Framework\Router\Responses\FileResponse;
use Framework\Router\HandlerResponse;

class FileResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $fileResponse = new FileResponse($handler);
        $this->assertInstanceOf(FileResponse::class, $fileResponse);
    }

    public function testSend()
    {
        $response = new \Framework\Router\HandlerResponse();
        $response->setResult(__DIR__ . '/test-file.txt');
        $fileResponse = new FileResponse($response);
        $fileResponse->send();
        $this->assertTrue(true); // Output method
    }
}
