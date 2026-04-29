<?php

use RBFrameworks\Core\InputUserOptions;

class InputUserOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $options = new InputUserOptions();
        $this->assertInstanceOf(InputUserOptions::class, $options);
    }

    public function testSetOption()
    {
        $options = new InputUserOptions();
        $result = $options->setOption('test', 'value');
        $this->assertInstanceOf(InputUserOptions::class, $result);
    }

    public function testGetOption()
    {
        $options = new InputUserOptions();
        $options->setOption('test', 'value');
        $this->assertEquals('value', $options->getOption('test'));
    }

    public function testGetOptionDefault()
    {
        $options = new InputUserOptions();
        $this->assertNull($options->getOption('nonexistent'));
    }

    public function testSetOptions()
    {
        $options = new InputUserOptions();
        $result = $options->setOptions(['a' => 1, 'b' => 2]);
        $this->assertInstanceOf(InputUserOptions::class, $result);
    }

    public function testGetOptions()
    {
        $options = new InputUserOptions();
        $options->setOptions(['a' => 1, 'b' => 2]);
        $result = $options->getOptions();
        $this->assertIsArray($result);
    }

    public function testHasOption()
    {
        $options = new InputUserOptions();
        $options->setOption('test', 'value');
        $this->assertTrue($options->hasOption('test'));
    }

    public function testRemoveOption()
    {
        $options = new InputUserOptions();
        $options->setOption('test', 'value');
        $result = $options->removeOption('test');
        $this->assertInstanceOf(InputUserOptions::class, $result);
    }
}
