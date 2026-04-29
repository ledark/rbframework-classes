<?php

use RBFrameworks\Core\Types\Cnpj;
use RBFrameworks\Core\Exceptions\CoreTypeException;

class CnpjTest extends \PHPUnit\Framework\TestCase
{
    public function testCnpjValidCreation()
    {
        $cnpj = new Cnpj('11.222.333/0001-81');
        $this->assertInstanceOf(Cnpj::class, $cnpj);
    }

    public function testCnpjGetNumber()
    {
        $cnpj = new Cnpj('11.222.333/0001-81');
        $this->assertEquals(11222333000181, $cnpj->getNumber());
    }

    public function testCnpjGetFormatted()
    {
        $cnpj = new Cnpj('11222333000181');
        $this->assertEquals('11.222.333/0001-81', $cnpj->getFormatted());
    }

    public function testCnpjToString()
    {
        $cnpj = new Cnpj('11.222.333/0001-81');
        $this->assertEquals('11222333000181', (string) $cnpj);
    }

    public function testCnpjInvalidFormatThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cnpj('123');
    }

    public function testCnpjInvalidTypeThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cnpj('ab.cde.fgh/ijkl-mn');
    }

    public function testCnpjAllSameDigitsThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cnpj('11.111.111/1111-11');
    }

    public function testCnpjInvalidDVThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cnpj('12.345.678/0001-00');
    }

    public function testCnpjWithoutExceptionMode()
    {
        $cnpj = new Cnpj('12.345.678/0001-00', false);
        $this->assertFalse($cnpj->getValue());
    }

    public function testCnpjCleanFormat()
    {
        $cnpj = new Cnpj('11 222 333 0001 81');
        $this->assertEquals('11222333000181', $cnpj->getValue());
    }

    public function testCnpjWithBackslash()
    {
        $cnpj = new Cnpj('11.222.333\\0001-81');
        $this->assertEquals('11222333000181', $cnpj->getValue());
    }
}
