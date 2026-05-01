<?php

use RBFrameworks\Core\Modulo;

class ModuloTest extends \PHPUnit\Framework\TestCase
{
    public function testHasMethodExists()
    {
        $this->assertTrue(method_exists(Modulo::class, 'has'));
        $this->assertTrue(method_exists(Modulo::class, 'hasAny'));
        $this->assertTrue(method_exists(Modulo::class, 'hasAll'));
    }

    public function testGetInstance()
    {
        $modulo = Modulo::getInstance();
        $this->assertInstanceOf(Modulo::class, $modulo);
    }

    public function testHasReturnsBool()
    {
        $result = Modulo::has('non-existent-module-' . uniqid());
        $this->assertIsBool($result);
    }

    public function testHasAnyReturnsBool()
    {
        $result = Modulo::hasAny(['non-existent-module-' . uniqid()]);
        $this->assertIsBool($result);
    }

    public function testHasAllReturnsBool()
    {
        $result = Modulo::hasAll(['non-existent-module-' . uniqid()]);
        $this->assertIsBool($result);
    }

    public function testGetInstanceSameInstance()
    {
        $instance1 = Modulo::getInstance();
        $instance2 = Modulo::getInstance();
        $this->assertSame($instance1, $instance2);
    }
}
