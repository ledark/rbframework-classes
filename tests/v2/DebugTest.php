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
        Debug::devValue('test_value');
        $this->assertTrue(true);
    }

    public function testDevCard()
    {
        Debug::devCard('test message');
        $this->assertTrue(true);
    }

    public function testLog()
    {
        $result = Debug::log('test', ['param']);
        $this->assertIsBool($result);
    }
}
