<?php

use RBFrameworks\Core\Api;

class ApiTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $api = new Api();
        $this->assertInstanceOf(Api::class, $api);
    }

    public function testAddNamespace()
    {
        $api = new Api();
        $result = $api->addNamespace('\Test\Namespace');
        $this->assertInstanceOf(Api::class, $result);
    }

    public function testAddNamespaceWithMount()
    {
        $api = new Api();
        $result = $api->addNamespace('\Test\Namespace', '/api');
        $this->assertInstanceOf(Api::class, $result);
    }

    public function testGetRoutes()
    {
        $api = new Api();
        $result = $api->getRoutes();
        $this->assertIsArray($result);
    }

    public function testSet404()
    {
        $api = new Api();
        $result = $api->set404(function() { return '404'; });
        $this->assertInstanceOf(Api::class, $result);
    }

    public function testGetResponseJson()
    {
        $this->assertEquals('json', Api::getResponse('@response json'));
    }

    public function testGetResponseHtml()
    {
        $this->assertEquals('html', Api::getResponse('@response html'));
    }

    public function testGetResponseText()
    {
        $this->assertEquals('text', Api::getResponse('@response text'));
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, Api::getStatusCode('@status 200'));
    }

    public function testGetStatusCodeDefault()
    {
        $this->assertEquals(200, Api::getStatusCode('no status'));
    }

    public function testGetUtf8True()
    {
        $this->assertTrue(Api::getUtf8('@utf8 true'));
    }

    public function testGetUtf8False()
    {
        $this->assertFalse(Api::getUtf8('@utf8 false'));
    }

    public function testGetBefore()
    {
        $this->assertEquals('someFunction', Api::getBefore('@before someFunction'));
    }

    public function testGetDescr()
    {
        $this->assertEquals('Test description', Api::getDescr('@descr Test description'));
    }

    public function testGetCache()
    {
        $this->assertEquals('config_key', Api::getCache('@cache config_key'));
    }
}
