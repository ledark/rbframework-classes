<?php

use RBFrameworks\Core\Modulo;

class ModuloTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $modulo = new Modulo('Test Module');
        $this->assertInstanceOf(Modulo::class, $modulo);
    }

    public function testGetName()
    {
        $modulo = new Modulo('Test Module');
        $this->assertEquals('Test Module', $modulo->getName());
    }

    public function testSetName()
    {
        $modulo = new Modulo('Old Name');
        $modulo->setName('New Name');
        $this->assertEquals('New Name', $modulo->getName());
    }

    public function testGetSlug()
    {
        $modulo = new Modulo('Test Module');
        $result = $modulo->getSlug();
        $this->assertIsString($result);
    }

    public function testGetIcon()
    {
        $modulo = new Modulo('Test');
        $this->assertIsString($modulo->getIcon());
    }

    public function testSetIcon()
    {
        $modulo = new Modulo('Test');
        $result = $modulo->setIcon('fa fa-test');
        $this->assertInstanceOf(Modulo::class, $result);
    }

    public function testGetRoute()
    {
        $modulo = new Modulo('Test');
        $this->assertIsString($modulo->getRoute());
    }

    public function testSetRoute()
    {
        $modulo = new Modulo('Test');
        $result = $modulo->setRoute('/test');
        $this->assertInstanceOf(Modulo::class, $result);
    }

    public function testIsVisible()
    {
        $modulo = new Modulo('Test');
        $this->assertIsBool($modulo->isVisible());
    }

    public function testSetVisible()
    {
        $modulo = new Modulo('Test');
        $result = $modulo->setVisible(false);
        $this->assertInstanceOf(Modulo::class, $result);
    }
}
