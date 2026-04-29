<?php

use Framework\HttpCodes;

class HttpCodesTest extends \PHPUnit\Framework\TestCase
{
    public function testGet200()
    {
        $this->assertEquals('OK', HttpCodes::get(200));
    }

    public function testGet404()
    {
        $this->assertEquals('Not Found', HttpCodes::get(404));
    }

    public function testGet500()
    {
        $this->assertEquals('Internal Server Error', HttpCodes::get(500));
    }

    public function testGet301()
    {
        $this->assertEquals('Moved Permanently', HttpCodes::get(301));
    }

    public function testGet403()
    {
        $this->assertEquals('Forbidden', HttpCodes::get(403));
    }

    public function testGetCodes()
    {
        $codes = HttpCodes::getCodes();
        $this->assertIsArray($codes);
        $this->assertArrayHasKey(200, $codes);
    }

    public function testGetCodeByNameOK()
    {
        $result = HttpCodes::getCodeByName('OK');
        $this->assertEquals(200, $result);
    }

    public function testGetCodeByNameNotFound()
    {
        $result = HttpCodes::getCodeByName('Not Found');
        $this->assertEquals(404, $result);
    }

    public function testGetCodeByNamePartial()
    {
        $result = HttpCodes::getCodeByName('server');
        $this->assertEquals(500, $result);
    }

    public function testGetCodeByNameNotFoundReturn()
    {
        $result = HttpCodes::getCodeByName('nonexistent code xyz');
        $this->assertFalse($result);
    }

    public function testGet100()
    {
        $this->assertEquals('Continue', HttpCodes::get(100));
    }

    public function testGet204()
    {
        $this->assertEquals('No Content', HttpCodes::get(204));
    }

    public function testGet302()
    {
        $this->assertEquals('Found', HttpCodes::get(302));
    }

    public function testGet418()
    {
        $this->assertEquals("I'm a teapot", HttpCodes::get(418));
    }

    public function testGet503()
    {
        $this->assertEquals('Service Unavailable', HttpCodes::get(503));
    }
}
