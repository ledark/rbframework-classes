<?php

namespace Tests\Types;

use RBFrameworks\Core\Types\Cep;

class CepTest extends \PHPUnit\Framework\TestCase
{
    public function testCepCreation()
    {
        $cep = new Cep('01001-000');
        $this->assertInstanceOf(Cep::class, $cep);
    }

    public function testCepGetNumber()
    {
        $cep = new Cep('01001-000');
        $this->assertEquals('01001000', $cep->getNumber());
    }

    public function testCepGetFormatted()
    {
        $cep = new Cep('01001000');
        $this->assertEquals('01001-000', $cep->getFormatted());
    }

    public function testCepIsValid()
    {
        $cep = new Cep('01001-000');
        $this->assertTrue($cep->isValid());
    }

    public function testCepIsValidWithJustNumbers()
    {
        $cep = new Cep('01001000');
        $this->assertTrue($cep->isValid());
    }

    public function testCepInvalidEmpty()
    {
        $cep = new Cep('');
        $this->assertFalse($cep->isValid());
    }

    public function testCepInvalidAllSameDigits()
    {
        $cep = new Cep('00000000');
        $this->assertFalse($cep->isValid());
    }

    public function testCepInvalidLength()
    {
        $cep = new Cep('123');
        $this->assertFalse($cep->isValid());
    }

    public function testCepWithDots()
    {
        $cep = new Cep('01.001-000');
        $this->assertEquals('01001000', $cep->getNumber());
    }

    public function testCepGetDetail()
    {
        $cep = new Cep('01001-000');
        $detail = $cep->getDetail();
        $this->assertIsArray($detail);
    }
}
