<?php

use Framework\Session;

class SessionV1Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $session = Session::getInstance();
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testSetAndGet()
    {
        Session::set('test_key', 'test_value');
        $result = Session::get('test_key');
        $this->assertEquals('test_value', $result);
    }

    public function testGetWithDefault()
    {
        $result = Session::get('nonexistent_key', 'default_value');
        $this->assertEquals('default_value', $result);
    }

    public function testHas()
    {
        Session::set('has_test', 'value');
        $this->assertTrue(Session::has('has_test'));
    }

    public function testHasNot()
    {
        $this->assertFalse(Session::has('nonexistent_key_12345'));
    }

    public function testRemove()
    {
        Session::set('remove_test', 'value');
        $this->assertTrue(Session::has('remove_test'));
        Session::remove('remove_test');
        $this->assertFalse(Session::has('remove_test'));
    }

    public function testClear()
    {
        Session::set('key1', 'value1');
        Session::set('key2', 'value2');
        Session::clear();
        $this->assertFalse(Session::has('key1'));
        $this->assertFalse(Session::has('key2'));
    }

    public function testSetArray()
    {
        $array = ['foo' => 'bar'];
        Session::set('array_key', $array);
        $result = Session::get('array_key');
        $this->assertEquals($array, $result);
    }
}
