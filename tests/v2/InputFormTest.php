<?php

use RBFrameworks\Core\InputForm;
use RBFrameworks\Core\InputUserOptions;

class InputFormTest extends \PHPUnit\Framework\TestCase
{
    public function testGetFieldNumber()
    {
        $result = InputForm::getFieldNumber('test_field', 42);
        $this->assertIsInt($result);
    }

    public function testGetFieldText()
    {
        $result = InputForm::getFieldText('test_field', 'default');
        $this->assertIsString($result);
    }

    public function testGetFieldArray()
    {
        $result = InputForm::getFieldArray('test_field', ['default']);
        $this->assertIsArray($result);
    }

    public function testGetFieldTextarea()
    {
        $result = InputForm::getFieldTextarea('test_field', 'default text');
        $this->assertIsString($result);
    }

    public function testGetFieldWithOptions()
    {
        $options = new InputUserOptions();
        $options->default = 'test_default';
        $result = InputForm::getField('test_field', $options);
        $this->assertEquals('test_default', $result);
    }

    public function testGetFromGET()
    {
        $result = InputForm::getFromGET();
        $this->assertIsArray($result);
    }

    public function testGetFromPOST()
    {
        $result = InputForm::getFromPOST();
        $this->assertIsArray($result);
    }

    public function testGetFromAnywhere()
    {
        $result = InputForm::getFromAnywhere();
        $this->assertIsArray($result);
    }

    public function testGetFromUri()
    {
        $result = InputForm::getFromUri(-1);
        $this->assertIsString($result);
    }
}
