<?php

use RBFrameworks\Core\Input;

class InputV2Test extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // Reset input instance
        $reflection = new \ReflectionClass(Input::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }

    public function testHasGET()
    {
        $_GET = ['test' => 'value'];
        $result = Input::hasGET();
        $this->assertIsBool($result);
    }

    public function testHasPOST()
    {
        $_POST = ['test' => 'value'];
        $result = Input::hasPOST();
        $this->assertIsBool($result);
    }

    public function testHasSESSION()
    {
        $_SESSION = ['test' => 'value'];
        $result = Input::hasSESSION();
        $this->assertIsBool($result);
    }

    public function testGetField()
    {
        $_POST = ['field1' => 'value1'];
        $result = Input::getField('field1');
        $this->assertEquals('value1', $result);
    }

    public function testGetFieldWithDefault()
    {
        $result = Input::getField('nonexistent_field', 'default');
        $this->assertEquals('default', $result);
    }

    public function testGetAll()
    {
        $_POST = ['key1' => 'val1'];
        $result = Input::getAll();
        $this->assertIsArray($result);
    }

    public function testHasAllInputFields()
    {
        $_POST = ['field1' => 'val1', 'field2' => 'val2'];
        $result = Input::hasAllInputFields(['field1', 'field2']);
        $this->assertTrue($result);
    }

    public function testHasAllInputFieldsFalse()
    {
        $_POST = ['field1' => 'val1'];
        $result = Input::hasAllInputFields(['field1', 'field2']);
        $this->assertFalse($result);
    }

    public function testHasAnyInputFields()
    {
        $_POST = ['field1' => 'val1'];
        $result = Input::hasAnyInputFields(['field1', 'field2']);
        $this->assertTrue($result);
    }

    public function testHasAnyInputFieldsFalse()
    {
        $_POST = ['field1' => 'val1'];
        $result = Input::hasAnyInputFields(['field2', 'field3']);
        $this->assertFalse($result);
    }

    public function testGetFromFirstField()
    {
        $_POST = ['field1' => 'value1'];
        $result = Input::getFromFirstField(['field1', 'field2']);
        $this->assertEquals('value1', $result);
    }

    public function testHasHeader()
    {
        $result = Input::hasHeader('User-Agent');
        $this->assertIsBool($result);
    }
}
