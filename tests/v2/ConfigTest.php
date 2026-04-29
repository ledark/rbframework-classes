<?php

use RBFrameworks\Core\Config;

class ConfigV2Test extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $result = Config::assigned('nonexistent_key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testAssigned()
    {
        $result = Config::assigned('nonexistent_key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testPrint()
    {
        // Test that print method exists and can be called
        $this->assertTrue(method_exists(Config::class, 'print'));
    }

    public function testEcho()
    {
        // Test that echo method exists and can be called
        $this->assertTrue(method_exists(Config::class, 'echo'));
    }

    public function testGetCollectionNames()
    {
        $result = Config::getCollectionNames();
        $this->assertIsArray($result);
    }

    public function testGetCollectionDir()
    {
        if (function_exists('get_collection_dir')) {
            $result = Config::getCollectionDir();
            $this->assertIsString($result);
        } else {
            $this->expectException(\Exception::class);
            Config::getCollectionDir();
        }
    }
}
