<?php

use RBFrameworks\Core\Debug;

class DebugV2Test extends \PHPUnit\Framework\TestCase
{
    public function testIsLocalhost()
    {
        $result = Debug::isLocalhost();
        $this->assertIsBool($result);
    }

    public function testIsDeveloper()
    {
        // Mock or skip if function doesn't exist
        if (!function_exists('is_developer')) {
            $this->markTestSkipped('is_developer function not available');
        }
        $result = Debug::isDeveloper();
        $this->assertIsBool($result);
    }

    public function testDisplayErrors()
    {
        Debug::displayErrors(false);
        $this->assertTrue(true);
    }

    public function testGetPrintableAsText()
    {
        $result = Debug::getPrintableAsText('test');
        $this->assertIsString($result);
    }

    public function testGetPrintableAsArray()
    {
        $result = Debug::getPrintableAsArray('test');
        $this->assertIsArray($result);
    }

    public function testDevValue()
    {
        // This method outputs HTML, just test it doesn't throw exception
        Debug::devValue('test_value');
        $this->assertTrue(true);
    }

    public function testDevCard()
    {
        // This method outputs HTML, just test it doesn't throw exception
        Debug::devCard('test message');
        $this->assertTrue(true);
    }

    public function testLog()
    {
        // log method returns void, not bool
        Debug::log('test', ['param']);
        $this->assertTrue(true);
    }
}
