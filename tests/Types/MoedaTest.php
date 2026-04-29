<?php

use RBFrameworks\Core\Types\Moeda;

class MoedaTest extends \PHPUnit\Framework\TestCase
{
    public function testMoedaCreation()
    {
        $moeda = new Moeda('R$ 1.234,56');
        $this->assertInstanceOf(Moeda::class, $moeda);
    }

    public function testMoedaGetNumber()
    {
        $moeda = new Moeda('R$ 1.234,56');
        $this->assertEquals(123456, $moeda->getNumber());
    }

    public function testMoedaGetDecimal()
    {
        $moeda = new Moeda('R$ 1.234,56');
        $this->assertEquals('1234.56', $moeda->getDecimal());
    }

    public function testMoedaGetFormatted()
    {
        $moeda = new Moeda('R$ 1.234,56');
        $this->assertEquals('1.234,56', $moeda->getFormatted());
    }

    public function testMoedaGetFullFormatted()
    {
        $moeda = new Moeda('R$ 1.234,56');
        $this->assertEquals('R$ 1.234,56', $moeda->getFullFormatted());
    }

    public function testMoedaWithPrefix()
    {
        $moeda = new Moeda('1.234,56');
        $this->assertEquals('US$ 1.234,56', $moeda->getFullFormatted('US$ '));
    }

    public function testMoedaNegative()
    {
        $moeda = new Moeda('-1.234,56');
        $this->assertTrue($moeda->getNumber() < 0);
    }

    public function testMoedaJustNumbers()
    {
        $moeda = new Moeda('123456');
        $this->assertEquals(123456, $moeda->getNumber());
    }

    public function testMoedaIncreasePercentual()
    {
        $moeda = new Moeda('100,00');
        $moeda->increasePercentual(0.1); // 10% increase
        $this->assertEquals(11000, $moeda->getNumber());
    }

    public function testMoedaDecreasePercentual()
    {
        $moeda = new Moeda('100,00');
        $moeda->decreasePercentual(0.1); // 10% decrease
        $this->assertEquals(9000, $moeda->getNumber());
    }

    public function testMoedaZero()
    {
        $moeda = new Moeda('0,00');
        $this->assertEquals(0, $moeda->getNumber());
    }
}
