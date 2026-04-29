<?php

use RBFrameworks\Core\Utils\Arrays;

class ArraysV2Test extends \PHPUnit\Framework\TestCase
{
    public function testGetValueByKeySimple()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals('bar', Arrays::getValueByKey('foo', $data));
    }

    public function testGetValueByKeyNested()
    {
        $data = ['foo' => ['bar' => 'baz']];
        $this->assertEquals('baz', Arrays::getValueByKey('foo.bar', $data));
    }

    public function testGetValueByKeyNotFound()
    {
        $data = ['foo' => 'bar'];
        $this->assertNull(Arrays::getValueByKey('invalid', $data));
    }

    public function testGetValueByKeyWithDefault()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals('default', Arrays::getValueByKey('invalid', $data, 'default'));
    }

    public function testGetValueByDotKeySimple()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals('bar', Arrays::getValueByDotKey('foo', $data));
    }

    public function testGetValueByDotKeyNested()
    {
        $data = ['foo' => ['bar' => 'baz']];
        $this->assertEquals('baz', Arrays::getValueByDotKey('foo.bar', $data));
    }

    public function testIsAssocTrue()
    {
        $data = ['a' => 'foo', 'b' => 'bar'];
        $this->assertTrue(Arrays::isAssoc($data));
    }

    public function testIsAssocFalse()
    {
        $data = ['foo', 'bar', 'baz'];
        $this->assertFalse(Arrays::isAssoc($data));
    }

    public function testSanitize()
    {
        $result = Arrays::sanitize("test'string");
        $this->assertIsString($result);
    }

    public function testSanitizeArray()
    {
        $result = Arrays::sanitize(['test', 'string']);
        $this->assertIsArray($result);
    }

    public function testExtractKeysFromAssocArray()
    {
        $model = ['field1' => ['mysql' => true], 'field2' => ['mysql' => false]];
        $result = Arrays::extractKeysFromAssocArray($model);
        $this->assertContains('field1', $result);
    }

    public function testCountElements()
    {
        $data = ['a' => 'foo', 'b' => ['c' => 'bar']];
        $result = Arrays::countElements($data);
        $this->assertIsInt($result);
    }

    public function testSetValueByKey()
    {
        $data = ['foo' => 'bar'];
        $result = Arrays::setValueByKey('new_key', $data, 'new_value');
        $this->assertTrue($result);
    }

    public function testSetValueByDotKey()
    {
        $data = [];
        $result = Arrays::setValueByDotKey('foo.bar', $data, 'value');
        $this->assertNotNull($result);
    }
}
