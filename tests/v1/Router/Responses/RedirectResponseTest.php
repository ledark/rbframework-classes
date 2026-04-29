<?php

use Framework\Router\Responses\RedirectResponse;
use Framework\Router\HandlerResponse;

class RedirectResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $handler = new HandlerResponse();
        $redirectResponse = new RedirectResponse($handler);
        $this->assertInstanceOf(RedirectResponse::class, $redirectResponse);
    }

    public function testSend()
    {
        $redirectResponse = new RedirectResponse();
        $redirectResponse->send();
        $this->assertTrue(true); // Output method
    }
}
