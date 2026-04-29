<?php

use RBFrameworks\Core\Types\Phone;

class PhoneTest extends \PHPUnit\Framework\TestCase
{
    public function testPhoneCreation()
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertInstanceOf(Phone::class, $phone);
    }

    public function testPhoneGetNumber()
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertEquals(11999999999, $phone->getNumber());
    }

    public function testPhoneToString()
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function testPhoneFormatted8Digits()
    {
        $phone = new Phone('1234-5678');
        $this->assertEquals('(XX) 1234-5678', $phone->getFormatted());
    }

    public function testPhoneFormatted9Digits()
    {
        $phone = new Phone('12345-6789');
        $this->assertEquals('(XX) 12345-6789', $phone->getFormatted());
    }

    public function testPhoneFormatted10Digits()
    {
        $phone = new Phone('(11) 1234-5678');
        $this->assertEquals('(11) 1234-5678', $phone->getFormatted());
    }

    public function testPhoneFormatted11Digits()
    {
        $phone = new Phone('(11) 91234-5678');
        $this->assertEquals('(11) 91234-5678', $phone->getFormatted());
    }

    public function testPhoneFormatted12Digits()
    {
        $phone = new Phone('551112345678');
        $this->assertStringContainsString('55', $phone->getFormatted());
    }

    public function testPhoneFormatted13Digits()
    {
        $phone = new Phone('5511912345678');
        $this->assertStringContainsString('55', $phone->getFormatted());
    }

    public function testPhoneCleanFormat()
    {
        $phone = new Phone('(11) 9.9999-9999');
        $this->assertEquals('11999999999', $phone->getNumber());
    }

    public function testPhoneInvalidThrowsException()
    {
        $this->expectException(\Exception::class);
        new Phone('abc');
    }
}
