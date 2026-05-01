<?php

use RBFrameworks\Core\Types\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $json = new Json('{"key":"value"}');
        $this->assertInstanceOf(Json::class, $json);
    }

    public function testConstructorWithArray()
    {
        $json = new Json(['key' => 'value']);
        $this->assertInstanceOf(Json::class, $json);
    }

    public function testGetString()
    {
        $json = new Json(['key' => 'value']);
        $result = $json->getString();
        $this->assertIsString($result);
    }

    public function testGetArray()
    {
        $json = new Json('{"key":"value"}');
        $result = $json->getArray();
        $this->assertIsArray($result);
    }

    public function testGetFormatted()
    {
        $json = new Json('{"key":"value"}');
        $result = $json->getFormatted();
        $this->assertIsString($result);
    }

    public function testGetNumber()
    {
        $json = new Json('{"key":"value"}');
        $result = $json->getNumber();
        $this->assertEquals(-1, $result);
    }

    public function testGetValueWithArray()
    {
        $json = new Json(['key' => 'value']);
        $result = $json->getValue();
        $this->assertIsString($result); // For array input, getValue returns getString()
    }

    public function testGetValueWithString()
    {
        $json = new Json('{"key":"value"}');
        $result = $json->getValue();
        $this->assertIsArray($result); // For string input, getValue returns getArray()
    }

    public function testSetOption()
    {
        $json = new Json('test');
        $json->setOption('forceUtf8', 'true'); // Note: setOption expects string value
        $result = $json->getOption('forceUtf8');
        $this->assertEquals('true', $result);
    }

    public function testGetOptionDefault()
    {
        $json = new Json('test');
        $result = $json->getOption('nonexistent', 'default');
        $this->assertEquals('default', $result);
    }

    public function testJsonEncodeNice()
    {
        $result = Json::json_encode_nice(['key' => 'value']);
        $this->assertIsString($result);
    }

    public function testJsonDecodeNice()
    {
        $result = Json::json_decode_nice('{"key":"value"}', true);
        $this->assertIsArray($result);
    }
}
