<?php

use RBFrameworks\Core\Validator\Rule;

class RuleTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValidDefault()
    {
        $rule = new Rule();
        $this->assertFalse($rule->isValid());
    }

    public function testSetValidStateTrue()
    {
        $rule = new Rule();
        $rule->setValidState(true);
        $this->assertTrue($rule->isValid());
    }

    public function testSetValidStateFalse()
    {
        $rule = new Rule();
        $rule->setValidState(false);
        $this->assertFalse($rule->isValid());
    }
}
