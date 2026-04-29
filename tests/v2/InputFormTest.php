<?php

use RBFrameworks\Core\InputForm;

class InputFormTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $inputForm = new InputForm();
        $this->assertInstanceOf(InputForm::class, $inputForm);
    }

    public function testSetField()
    {
        $inputForm = new InputForm();
        $result = $inputForm->setField('test_field', 'value');
        $this->assertInstanceOf(InputForm::class, $result);
    }

    public function testGetField()
    {
        $inputForm = new InputForm();
        $inputForm->setField('test', 'value');
        $this->assertEquals('value', $inputForm->getField('test'));
    }

    public function testGetFieldDefault()
    {
        $inputForm = new InputForm();
        $this->assertNull($inputForm->getField('nonexistent'));
    }

    public function testSetFields()
    {
        $inputForm = new InputForm();
        $result = $inputForm->setFields(['a' => 1, 'b' => 2]);
        $this->assertInstanceOf(InputForm::class, $result);
    }

    public function testGetFields()
    {
        $inputForm = new InputForm();
        $inputForm->setFields(['a' => 1, 'b' => 2]);
        $result = $inputForm->getFields();
        $this->assertIsArray($result);
    }

    public function testRender()
    {
        $inputForm = new InputForm();
        $result = $inputForm->render();
        $this->assertIsString($result);
    }

    public function testValidate()
    {
        $inputForm = new InputForm();
        $result = $inputForm->validate();
        $this->assertIsBool($result);
    }
}
