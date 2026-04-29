<?php

use RBFrameworks\Core\Chance;

class ChanceV2Test extends \PHPUnit\Framework\TestCase
{
    public function testPhone()
    {
        $result = Chance::phone();
        $this->assertIsString($result);
    }

    public function testPhoneMobile()
    {
        $result = Chance::phone(true);
        $this->assertIsString($result);
    }

    public function testPhoneOnlyNumbers()
    {
        $result = Chance::phone(null, true);
        $this->assertIsString($result);
    }

    public function testNome()
    {
        $result = Chance::nome();
        $this->assertIsString($result);
    }

    public function testNomeMasculino()
    {
        $result = Chance::nome('masc');
        $this->assertIsString($result);
    }

    public function testNomeFeminino()
    {
        $result = Chance::nome('fem');
        $this->assertIsString($result);
    }

    public function testPorcentTrue()
    {
        $result = Chance::porcent(100);
        $this->assertTrue($result);
    }

    public function testPorcentFalse()
    {
        $result = Chance::porcent(0);
        $this->assertFalse($result);
    }

    public function testPickOne()
    {
        $array = ['a', 'b', 'c'];
        $result = Chance::pickOne($array);
        $this->assertContains($result, $array);
    }

    public function testCpf()
    {
        $result = Chance::cpf();
        $this->assertIsString($result);
        $this->assertStringContainsString('.', $result);
    }

    public function testCpfWithoutFormat()
    {
        $result = Chance::cpf(false);
        $this->assertIsString($result);
    }

    public function testCnpj()
    {
        $result = Chance::cnpj();
        $this->assertIsString($result);
    }

    public function testCnpjWithoutFormat()
    {
        $result = Chance::cnpj(false);
        $this->assertIsString($result);
    }

    public function testAge()
    {
        $result = Chance::age(18, 30, 60);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(18, $result);
        $this->assertLessThanOrEqual(60, $result);
    }

    public function testLastname()
    {
        $result = Chance::lastname();
        $this->assertIsString($result);
    }

    public function testPais()
    {
        $result = Chance::pais();
        $this->assertIsString($result);
    }

    public function testProb()
    {
        $result = Chance::prob('test_string');
        $this->assertIsBool($result);
    }

    public function testUser()
    {
        $result = Chance::user();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('nome', $result);
    }
}
