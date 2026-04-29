<?php

use RBFrameworks\Core\Validator\Rules\Required;

class RequiredTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $required = new Required();
        $this->assertInstanceOf(Required::class, $required);
    }

    public function testExtendsRule()
    {
        $required = new Required();
        $this->assertInstanceOf(\RBFrameworks\Core\Validator\Rule::class, $required);
    }

    public function testIsValidDefault()
    {
        $required = new Required();
        $this->assertFalse($required->isValid());
    }
}
