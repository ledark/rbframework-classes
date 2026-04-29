<?php

use RBFrameworks\Core\Validator\PreConditions;

class PreConditionsTest extends \PHPUnit\Framework\TestCase
{
    public function testAnyTrue()
    {
        $result = PreConditions::any([false, true, false]);
        $this->assertTrue($result);
    }

    public function testAnyFalse()
    {
        $result = PreConditions::any([false, false, false]);
        $this->assertFalse($result);
    }

    public function testAnyEmpty()
    {
        $result = PreConditions::any([]);
        $this->assertFalse($result);
    }

    public function testAllTrue()
    {
        $result = PreConditions::all([true, true, true]);
        $this->assertTrue($result);
    }

    public function testAllFalse()
    {
        $result = PreConditions::all([true, false, true]);
        $this->assertFalse($result);
    }

    public function testAllEmpty()
    {
        $result = PreConditions::all([]);
        $this->assertTrue($result);
    }

    public function testAnyWithMixed()
    {
        $result = PreConditions::any([0, '', null, true]);
        $this->assertTrue($result);
    }

    public function testAllWithMixed()
    {
        $result = PreConditions::all([1, true, -1]);
        $this->assertTrue($result);
    }
}
