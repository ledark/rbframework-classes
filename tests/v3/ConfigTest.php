<?php

use RBFrameworks\Config;

class ConfigV3Test extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $result = Config::get('nonexistent_key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testGetWithNullDefault()
    {
        $result = Config::get('nonexistent_key');
        $this->assertNull($result);
    }
}
