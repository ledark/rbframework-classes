<?php

use Framework\Debug;

class DebugV1Test extends \PHPUnit\Framework\TestCase
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
        // Just verify the method can be called without error
        Debug::displayErrors(false);
        $this->assertTrue(true);
    }

    public function testMessage()
    {
        // Output method - just verify it runs
        ob_start();
        Debug::message('test message');
        $output = ob_get_clean();
        $this->assertStringContainsString('test message', $output);
    }

    public function testGetPrintableAsText()
    {
        $result = Debug::getPrintableAsText('test');
        $this->assertIsString($result);
    }

    public function testGetPrintableAsTextArray()
    {
        $result = Debug::getPrintableAsText(['a' => 'b']);
        $this->assertIsString($result);
    }
}
