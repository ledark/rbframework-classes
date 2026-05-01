<?php

use RBFrameworks\Core\InputUserOptions;

class InputUserOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $options = new InputUserOptions();
        $this->assertInstanceOf(InputUserOptions::class, $options);
    }

    public function testDefaultProperty()
    {
        $options = new InputUserOptions();
        $this->assertEquals('', $options->default);
    }

    public function testGetFromAnywhereProperty()
    {
        $options = new InputUserOptions();
        $this->assertIsBool($options->getFromAnywhere);
        $this->assertTrue($options->getFromAnywhere);
    }

    public function testDecodeUTF8Property()
    {
        $options = new InputUserOptions();
        $this->assertIsBool($options->decodeUTF8);
        $this->assertTrue($options->decodeUTF8);
    }

    public function testSanitizeProperty()
    {
        $options = new InputUserOptions();
        $this->assertIsBool($options->sanitize);
        $this->assertTrue($options->sanitize);
    }

    public function testSetProperties()
    {
        $options = new InputUserOptions();
        $options->default = 'test';
        $options->getFromAnywhere = false;
        $options->decodeUTF8 = false;
        $options->sanitize = false;
        
        $this->assertEquals('test', $options->default);
        $this->assertFalse($options->getFromAnywhere);
        $this->assertFalse($options->decodeUTF8);
        $this->assertFalse($options->sanitize);
    }
}
