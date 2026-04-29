<?php

use Framework\Plugin;

class PluginV1Test extends \PHPUnit\Framework\TestCase
{
    public function testLoad()
    {
        // Test that load method exists
        $this->assertTrue(method_exists(Plugin::class, 'load'));
    }

    public function testIsLoaded()
    {
        $this->assertTrue(method_exists(Plugin::class, 'isLoaded'));
    }
}
