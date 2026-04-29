<?php

use Framework\Response;

class ResponseV1Test extends \PHPUnit\Framework\TestCase
{
    public function testJson()
    {
        // Test that method exists and can be called
        $this->assertTrue(method_exists(Response::class, 'json'));
    }

    public function testText()
    {
        $this->assertTrue(method_exists(Response::class, 'text'));
    }

    public function testJs()
    {
        $this->assertTrue(method_exists(Response::class, 'js'));
    }

    public function testCss()
    {
        $this->assertTrue(method_exists(Response::class, 'css'));
    }

    public function testXml()
    {
        $this->assertTrue(method_exists(Response::class, 'xml'));
    }

    public function testHtml()
    {
        $this->assertTrue(method_exists(Response::class, 'html'));
    }
}
