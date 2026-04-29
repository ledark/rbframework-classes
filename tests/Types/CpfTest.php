<?php

use RBFrameworks\Core\Types\Cpf;
use RBFrameworks\Core\Exceptions\CoreTypeException;

class CpfTest extends \PHPUnit\Framework\TestCase
{
    public function testCpfValidCreation()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertInstanceOf(Cpf::class, $cpf);
    }

    public function testCpfGetNumber()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertEquals(52998224725, $cpf->getNumber());
    }

    public function testCpfGetFormatted()
    {
        $cpf = new Cpf('52998224725');
        $this->assertEquals('529.982.247-25', $cpf->getFormatted());
    }

    public function testCpfToString()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertEquals('52998224725', (string) $cpf);
    }

    public function testCpfGetString()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertEquals('52998224725', $cpf->getString());
    }

    public function testCpfGetValue()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertEquals('52998224725', $cpf->getValue());
    }

    public function testCpfInvalidFormatThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cpf('123');
    }

    public function testCpfInvalidTypeThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cpf('abc.def.ghi-jk');
    }

    public function testCpfAllSameDigitsThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cpf('111.111.111-11');
    }

    public function testCpfInvalidDVThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Cpf('123.456.789-00');
    }

    public function testCpfWithoutExceptionMode()
    {
        $cpf = new Cpf('123.456.789-00', false);
        $this->assertFalse($cpf->getValue());
    }

    public function testCpfCleanFormat()
    {
        $cpf = new Cpf('529 982 247 25');
        $this->assertEquals('52998224725', $cpf->getValue());
    }

    public function testCpfGetValetring()
    {
        $cpf = new Cpf('529.982.247-25');
        $this->assertEquals('52998224725', $cpf->getValetring());
    }
}
