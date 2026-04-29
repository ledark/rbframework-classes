<?php

use RBFrameworks\Core\Types\Email;
use RBFrameworks\Core\Exceptions\CoreTypeException;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function testEmailValidCreation()
    {
        $email = new Email('test@example.com');
        $this->assertInstanceOf(Email::class, $email);
    }

    public function testEmailGetValue()
    {
        $email = new Email('TEST@EXAMPLE.COM');
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function testEmailToString()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function testEmailIsValid()
    {
        $email = new Email('test@example.com');
        $this->assertTrue($email->isValid());
    }

    public function testEmailInvalidNoAtThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Email('testexample.com');
    }

    public function testEmailInvalidNoDotThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Email('test@examplecom');
    }

    public function testEmailInvalidFormatThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Email('test@@example.com');
    }

    public function testEmailWithPlus()
    {
        $email = new Email('test+alias@example.com');
        $this->assertEquals('test+alias@example.com', $email->getValue());
    }

    public function testEmailWithSubdomain()
    {
        $email = new Email('test@sub.example.com');
        $this->assertEquals('test@sub.example.com', $email->getValue());
    }

    public function testEmailWithoutExceptionMode()
    {
        $email = new Email('invalid-email', false);
        $this->assertFalse($email->getValue());
    }

    public function testEmailMultipleDots()
    {
        $email = new Email('test.name@example.co.uk');
        $this->assertEquals('test.name@example.co.uk', $email->getValue());
    }
}
