<?php

use RBFrameworks\Core\InputUser;

class InputUserTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // Set up POST data for testing
        $_POST = ['name' => 'John', 'email' => 'test@example.com', 'btnSend' => 'Submit'];
    }

    public function testConstructor()
    {
        $inputUser = new InputUser();
        $this->assertInstanceOf(InputUser::class, $inputUser);
    }

    public function testGetResult()
    {
        $inputUser = new InputUser();
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testUnsetFields()
    {
        $inputUser = new InputUser();
        $inputUser->unsetFields(['btnSend']);
        $result = $inputUser->getResult();
        $this->assertArrayNotHasKey('btnSend', $result);
    }

    public function testUnsetFieldsThatAreNotInList()
    {
        $inputUser = new InputUser();
        $inputUser->unsetFieldsThatAreNotInList(['name', 'email']);
        $result = $inputUser->getResult();
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayNotHasKey('btnSend', $result);
    }

    public function testSanitize()
    {
        $inputUser = new InputUser();
        $inputUser->sanitize();
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testEncodeUTF8()
    {
        $inputUser = new InputUser();
        $inputUser->encodeUTF8();
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testDecodeUTF8()
    {
        $inputUser = new InputUser();
        $inputUser->decodeUTF8();
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testPreventNullFields()
    {
        $inputUser = new InputUser();
        $this->expectException(\Exception::class);
        $inputUser->preventNullFields(['nonexistent_field']);
    }

    public function testPreventEmptyFields()
    {
        $_POST = ['empty_field' => ''];
        $inputUser = new InputUser();
        $this->expectException(\Exception::class);
        $inputUser->preventEmptyFields(['empty_field']);
    }

    public function testValidateField()
    {
        $inputUser = new InputUser();
        $inputUser->validateField('name', function($value) {
            return !empty($value);
        });
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testCustomSanitizeField()
    {
        $inputUser = new InputUser();
        $inputUser->customSanitizeField('name', function($value) {
            return strtoupper($value);
        });
        $result = $inputUser->getResult();
        $this->assertEquals('JOHN', $result['name']);
    }

    public function testField()
    {
        $inputUser = new InputUser();
        $result = $inputUser->field('name');
        $this->assertEquals('John', $result);
    }

    public function testFieldWithDefault()
    {
        $inputUser = new InputUser();
        $result = $inputUser->field('nonexistent', 'default');
        $this->assertEquals('default', $result);
    }

    public function testSet()
    {
        $inputUser = new InputUser();
        $inputUser->set('new_field', 'new_value');
        $result = $inputUser->field('new_field');
        $this->assertEquals('new_value', $result);
    }

    public function testAssigned()
    {
        $inputUser = new InputUser();
        $inputUser->assigned('another_field', 'another_value');
        $result = $inputUser->field('another_field');
        $this->assertEquals('another_value', $result);
    }

    public function testGetFromPOST()
    {
        $inputUser = InputUser::getInputUser();
        $inputUser->getFromPOST();
        $result = $inputUser->getResult();
        $this->assertIsArray($result);
    }

    public function testGetSanitizedResult()
    {
        $result = InputUser::getSanitizedResult([]);
        $this->assertIsArray($result);
    }
}
