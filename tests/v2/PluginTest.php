<?php

use RBFrameworks\Core\Plugin;

class PluginV2Test extends \PHPUnit\Framework\TestCase
{
    public function testLoadMethodExists()
    {
        $this->assertTrue(method_exists(Plugin::class, 'load'));
    }

    public function testIsLoadedMethodNotExists()
    {
        // isLoaded method doesn't exist in Plugin class
        $this->assertFalse(method_exists(Plugin::class, 'isLoaded'));
    }

    public function testCallStaticMethodExists()
    {
        $this->assertTrue(method_exists(Plugin::class, '__callStatic'));
    }

    public function testLoadFunction()
    {
        // Test that load method exists and is callable
        $this->assertTrue(is_callable([Plugin::class, 'load']));
    }
}
