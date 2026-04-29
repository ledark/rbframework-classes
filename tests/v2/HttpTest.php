<?php

use RBFrameworks\Core\Http;

class HttpV2Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $http = new Http('http://example.com');
        $this->assertInstanceOf(Http::class, $http);
    }

    public function testSetMethod()
    {
        $http = new Http('http://example.com');
        $result = $http->setMethod('POST');
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testSetUri()
    {
        $http = new Http('http://example.com');
        $result = $http->setUri('http://newexample.com');
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testSetRequestData()
    {
        $http = new Http('http://example.com');
        $result = $http->setRequestData(['key' => 'value']);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAsGet()
    {
        $http = new Http('http://example.com');
        $result = $http->asGet();
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAsPost()
    {
        $http = new Http('http://example.com');
        $result = $http->asPost(['key' => 'value']);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAsPut()
    {
        $http = new Http('http://example.com');
        $result = $http->asPut();
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAsPatch()
    {
        $http = new Http('http://example.com');
        $result = $http->asPatch();
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAsDelete()
    {
        $http = new Http('http://example.com');
        $result = $http->asDelete();
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAddOption()
    {
        $http = new Http('http://example.com');
        $result = $http->addOption('timeout', 30);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testSetOptions()
    {
        $http = new Http('http://example.com');
        $result = $http->setOptions(['timeout' => 30]);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testAddOptions()
    {
        $http = new Http('http://example.com');
        $result = $http->addOptions(['timeout' => 30]);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testGetMethod()
    {
        $http = new Http('http://example.com');
        $http->setMethod('POST');
        $result = $http->getMethod();
        $this->assertEquals('POST', $result);
    }

    public function testGetUri()
    {
        $http = new Http('http://example.com');
        $result = $http->getUri();
        $this->assertEquals('http://example.com', $result);
    }

    public function testGetRequestData()
    {
        $http = new Http('http://example.com');
        $http->setRequestData(['key' => 'value']);
        $result = $http->getRequestData();
        $this->assertIsArray($result);
    }

    public function testGetOptions()
    {
        $http = new Http('http://example.com');
        $result = $http->getOptions();
        $this->assertIsArray($result);
    }

    public function testSetExpectedStatusCodes()
    {
        $http = new Http('http://example.com');
        $result = $http->setExpectedStatusCodes([200, 201]);
        $this->assertInstanceOf(Http::class, $result);
    }

    public function testIsAbsoluteTrue()
    {
        $result = Http::isAbsolute('http://example.com');
        $this->assertTrue($result);
    }

    public function testIsAbsoluteFalse()
    {
        $result = Http::isAbsolute('/relative/path');
        $this->assertFalse($result);
    }

    public function testSanitizeUri()
    {
        $result = Http::sanitizeUri('path/to/resource');
        $this->assertIsString($result);
    }

    public function testGetHost()
    {
        $result = Http::getHost();
        $this->assertIsString($result);
    }

    public function testGetSite()
    {
        $result = Http::getSite();
        $this->assertIsString($result);
    }
}
