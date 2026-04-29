<?php

use Framework\Utils\Arrays;

class ArraysTest extends \PHPUnit\Framework\TestCase
{
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

    public function testGetValueByDotKeyNotFound()
    {
        $data = ['foo' => 'bar'];
        $this->assertNull(Arrays::getValueByDotKey('invalid', $data));
    }

    public function testGetValueByDotKeyWithDefault()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals('default', Arrays::getValueByDotKey('invalid', $data, 'default'));
    }

    public function testGetValueByDotKeyEmptyKey()
    {
        $data = ['foo' => 'bar'];
        $this->assertNull(Arrays::getValueByDotKey('', $data));
    }

    public function testGetValueByDotKeyEmptyData()
    {
        $this->assertNull(Arrays::getValueByDotKey('foo', []));
    }

    public function testGetValueByDotKeyDeepNested()
    {
        $data = ['a' => ['b' => ['c' => 'deep']]];
        $this->assertEquals('deep', Arrays::getValueByDotKey('a.b.c', $data));
    }

    public function testGetValueByDotKeyCustomSeparator()
    {
        $data = ['foo' => ['bar' => 'baz']];
        $this->assertEquals('baz', Arrays::getValueByDotKey('foo-bar', $data, null, '-'));
    }

    public function testGetValueByDotKeyInvalidKey()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals('bar', Arrays::getValueByDotKey('foo', $data));
    }
}
