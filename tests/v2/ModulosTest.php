<?php

use RBFrameworks\Core\Modulos;

class ModulosTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $modulos = new Modulos();
        $this->assertInstanceOf(Modulos::class, $modulos);
    }

    public function testAddModulo()
    {
        $modulos = new Modulos();
        $modulo = new \RBFrameworks\Core\Modulo('Test');
        $result = $modulos->addModulo($modulo);
        $this->assertInstanceOf(Modulos::class, $result);
    }

    public function testGetModulos()
    {
        $modulos = new Modulos();
        $result = $modulos->getModulos();
        $this->assertIsArray($result);
    }

    public function testGetMenu()
    {
        $modulos = new Modulos();
        $result = $modulos->getMenu();
        $this->assertIsString($result);
    }

    public function testGetHtml()
    {
        $modulos = new Modulos();
        $result = $modulos->getHtml();
        $this->assertIsString($result);
    }

    public function testCount()
    {
        $modulos = new Modulos();
        $this->assertIsInt($modulos->count());
    }

    public function testIsEmpty()
    {
        $modulos = new Modulos();
        $this->assertIsBool($modulos->isEmpty());
    }

    public function testClear()
    {
        $modulos = new Modulos();
        $result = $modulos->clear();
        $this->assertInstanceOf(Modulos::class, $result);
    }
}
