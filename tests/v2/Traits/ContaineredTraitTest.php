<?php

use RBFrameworks\Core\Traits\ContaineredTrait;

class ContaineredTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testSetAndGet()
    {
        $obj = new class {
            use ContaineredTrait;
        };
        $obj->testProperty = 'test_value';
        $this->assertEquals('test_value', $obj->testProperty);
    }

    public function testSetMultipleProperties()
    {
        $obj = new class {
            use ContaineredTrait;
        };
        $obj->prop1 = 'value1';
        $obj->prop2 = 'value2';
        $this->assertEquals('value1', $obj->prop1);
        $this->assertEquals('value2', $obj->prop2);
    }

    public function testGetNonExistentPropertyThrowsException()
    {
        $this->expectException(\UnexpectedValueException::class);
        $obj = new class {
            use ContaineredTrait;
        };
        $value = $obj->nonExistentProperty;
    }

    public function testOverwriteProperty()
    {
        $obj = new class {
            use ContaineredTrait;
        };
        $obj->prop = 'value1';
        $obj->prop = 'value2';
        $this->assertEquals('value2', $obj->prop);
    }

    public function testSetIntegerValue()
    {
        $obj = new class {
            use ContaineredTrait;
        };
        $obj->number = 123;
        $this->assertEquals(123, $obj->number);
    }

    public function testSetArrayValue()
    {
        $obj = new class {
            use ContaineredTrait;
        };
        $obj->array = ['a', 'b'];
        $this->assertEquals(['a', 'b'], $obj->array);
    }
}
