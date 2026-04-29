<?php

use RBFrameworks\Core\Auth;

class AuthTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerateToken()
    {
        $token = Auth::generateToken();
        $this->assertIsString($token);
        $this->assertStringContainsString('-', $token);
    }

    public function testGenerateTokenWithSalt()
    {
        $token = Auth::generateToken('my_salt');
        $this->assertIsString($token);
    }

    public function testCheckTokenValid()
    {
        $token = Auth::generateToken();
        $result = Auth::checkToken($token);
        $this->assertIsBool($result);
    }

    public function testCheckTokenInvalid()
    {
        $result = Auth::checkToken('invalid-token-format');
        $this->assertFalse($result);
    }

    public function testGenerateTokenConditional()
    {
        $token = Auth::generateTokenConditional();
        $this->assertIsString($token);
    }

    public function testExtractIp()
    {
        $token = Auth::generateToken();
        $ip = Auth::extractIp($token);
        $this->assertIsString($ip);
    }

    public function testExtractDate()
    {
        $token = Auth::generateToken();
        $date = Auth::extractDate($token);
        $this->assertIsInt($date);
    }

    public function testGetEncriptIP()
    {
        $encryptedIP = Auth::getEncriptIP();
        $this->assertIsString($encryptedIP);
    }

    public function testHasBearerToken()
    {
        $result = Auth::hasBearerToken();
        $this->assertIsBool($result);
    }

    public function testGetSecret()
    {
        $reflection = new \ReflectionClass(Auth::class);
        $method = $reflection->getMethod('getSecret');
        $method->setAccessible(true);
        $result = $method->invoke(null, '');
        $this->assertIsString($result);
    }

    public function testGetUniqID()
    {
        $reflection = new \ReflectionClass(Auth::class);
        $method = $reflection->getMethod('getUniqID');
        $method->setAccessible(true);
        $result = $method->invoke(null);
        $this->assertIsString($result);
    }

    public function testResolveEncriptedIP()
    {
        $encryptedIP = Auth::getEncriptIP();
        $decryptedIP = Auth::resolveEncriptedIP($encryptedIP);
        $this->assertIsString($decryptedIP);
    }
}
