<?php

use RBFrameworks\Core\Types\Password;
use RBFrameworks\Core\Exceptions\CoreTypeException;

class PasswordTest extends \PHPUnit\Framework\TestCase
{
    public function testPasswordCreation()
    {
        $password = new Password('12345678');
        $this->assertInstanceOf(Password::class, $password);
    }

    public function testPasswordGetValue()
    {
        $password = new Password('12345678');
        $this->assertEquals('12345678', $password->getValue());
    }

    public function testPasswordGetEncrypted()
    {
        $password = new Password('12345678');
        $hash = $password->getEncrypted();
        $this->assertTrue(password_verify('12345678', $hash));
    }

    public function testPasswordHasMatch()
    {
        $hash = password_hash('12345678', PASSWORD_DEFAULT);
        $password = new Password('12345678');
        $this->assertTrue($password->hasMatch($hash));
    }

    public function testPasswordHasMatchFalse()
    {
        $hash = password_hash('different_password', PASSWORD_DEFAULT);
        $password = new Password('12345678');
        $this->assertFalse($password->hasMatch($hash));
    }

    public function testPasswordTooShortThrowsException()
    {
        $this->expectException(CoreTypeException::class);
        new Password('123');
    }

    public function testPasswordEncryptStatic()
    {
        $encrypted = Password::encrypt('test_string');
        $this->assertIsString($encrypted);
    }

    public function testPasswordDecryptStatic()
    {
        $encrypted = Password::encrypt('test_string');
        $decrypted = Password::decrypt($encrypted);
        $this->assertEquals('test_string', $decrypted);
    }

    public function testPasswordEncryptWithCustomKey()
    {
        $key = 'my_custom_key';
        $encrypted = Password::encrypt('test_string', $key);
        $decrypted = Password::decrypt($encrypted, $key);
        $this->assertEquals('test_string', $decrypted);
    }

    public function testPasswordMinLengthExact()
    {
        $password = new Password('12345678');
        $this->assertEquals('12345678', $password->getValue());
    }
}
