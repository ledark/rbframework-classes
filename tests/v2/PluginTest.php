<?php

use RBFrameworks\Core\Plugin;

class PluginV2Test extends \PHPUnit\Framework\TestCase
{
    public function testLoad()
    {
        $this->assertTrue(method_exists(Plugin::class, 'load'));
    }

    public function testIsLoaded()
    {
        $this->assertTrue(method_exists(Plugin::class, 'isLoaded'));
    }

    public function testLoadFunction()
    {
        // Test that load can be called
        Plugin::load('some_function');
        $this->assertTrue(true);
    }
}
