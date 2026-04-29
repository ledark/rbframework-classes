<?php

use Framework\Types\Variables;

class VariablesTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $var = new Variables('test');
        $this->assertInstanceOf(Variables::class, $var);
    }

    public function testGetStringFromBooleanTrue()
    {
        $var = new Variables(true);
        $this->assertEquals('true', $var->getString());
    }

    public function testGetStringFromBooleanFalse()
    {
        $var = new Variables(false);
        $this->assertEquals('false', $var->getString());
    }

    public function testGetStringFromInteger()
    {
        $var = new Variables(123);
        $this->assertEquals('123', $var->getString());
    }

    public function testGetStringFromDouble()
    {
        $var = new Variables(123.45);
        $this->assertEquals('123,45', $var->getString());
    }

    public function testGetStringFromString()
    {
        $var = new Variables('hello');
        $this->assertEquals('hello', $var->getString());
    }

    public function testGetStringFromArray()
    {
        $var = new Variables(['a', 'b']);
        $result = $var->getString();
        $this->assertIsString($result);
    }

    public function testGetStringFromObject()
    {
        $obj = new \stdClass();
        $var = new Variables($obj);
        $result = $var->getString();
        $this->assertIsString($result);
    }

    public function testGetStringFromNull()
    {
        $var = new Variables(null);
        $this->assertEquals('NULL', $var->getString());
    }

    public function testGetStringBadgedTrue()
    {
        $var = new Variables(true);
        $result = $var->getStringBadged();
        $this->assertStringContainsString('true', $result);
    }

    public function testGetStringBadgedFalse()
    {
        $var = new Variables(false);
        $result = $var->getStringBadged();
        $this->assertStringContainsString('false', $result);
    }

    public function testGetBoolFromBooleanTrue()
    {
        $var = new Variables(true);
        $this->assertTrue($var->getBool());
    }

    public function testGetBoolFromBooleanFalse()
    {
        $var = new Variables(false);
        $this->assertFalse($var->getBool());
    }

    public function testGetBoolFromInteger()
    {
        $var = new Variables(123);
        $this->assertTrue($var->getBool());
    }

    public function testGetBoolFromIntegerZero()
    {
        $var = new Variables(0);
        $this->assertFalse($var->getBool());
    }

    public function testGetBoolFromStringTrue()
    {
        $var = new Variables('true');
        $this->assertTrue($var->getBool());
    }

    public function testGetBoolFromStringFalse()
    {
        $var = new Variables('false');
        $this->assertFalse($var->getBool());
    }

    public function testGetIntFromBoolean()
    {
        $var = new Variables(true);
        $this->assertEquals(1, $var->getInt());
    }

    public function testGetIntFromInteger()
    {
        $var = new Variables(42);
        $this->assertEquals(42, $var->getInt());
    }

    public function testGetIntFromString()
    {
        $var = new Variables('123');
        $this->assertEquals(123, $var->getInt());
    }

    public function testGetIntFromArray()
    {
        $var = new Variables(['a']);
        $this->assertEquals(0, $var->getInt());
    }

    public function testGetIntFromNull()
    {
        $var = new Variables(null);
        $this->assertEquals(0, $var->getInt());
    }

    public function testGetArray()
    {
        $var = new Variables('test');
        $this->assertIsArray($var->getArray());
    }

    public function testGetObject()
    {
        $var = new Variables('test');
        $this->assertIsObject($var->getObject());
    }

    public function testToString()
    {
        $var = new Variables('test');
        $this->assertEquals('test', (string) $var);
    }
}
