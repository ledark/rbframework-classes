<?php

use Framework\Config;

class ConfigV1Test extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $result = Config::get('nonexistent_key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testAssigned()
    {
        $result = Config::assigned('nonexistent_key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testGetInfo()
    {
        $result = Config::getInfo('test.key');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('key', $result);
    }

    public function testGetWithInvalidKey()
    {
        $result = Config::get('invalid.key.that.does.not.exist');
        $this->assertNull($result);
    }

    public function testGetWithDefault()
    {
        $result = Config::get('invalid_key', 'my_default');
        $this->assertEquals('my_default', $result);
    }
}
