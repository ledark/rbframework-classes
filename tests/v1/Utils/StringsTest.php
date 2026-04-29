<?php

use Framework\Utils\Strings;

class StringsTest extends \PHPUnit\Framework\TestCase
{
    public function testViewLimit()
    {
        $string = "This is a long string that needs to be limited";
        $result = Strings::viewlimit($string, 20);
        $this->assertEquals("This is a long st...", $result);
    }

    public function testViewLimitShort()
    {
        $string = "Short";
        $result = Strings::viewlimit($string, 20);
        $this->assertEquals("Short", $result);
    }

    public function testVerMais()
    {
        $string = "This is a long string";
        $result = Strings::vermais($string, 10);
        $this->assertEquals("This is...", $result);
    }

    public function testDeformarEmail()
    {
        $result = Strings::deformar_email('user@example.com');
        $this->assertStringContainsString('@', $result);
    }

    public function testMaskFone8Digits()
    {
        $result = Strings::maskFone('12345678');
        $this->assertEquals('1234-5678', $result);
    }

    public function testMaskFone10Digits()
    {
        $result = Strings::maskFone('1123456789');
        $this->assertEquals('(11) 2345-6789', $result);
    }

    public function testMaskFone11Digits()
    {
        $result = Strings::maskFone('11912345678');
        $this->assertEquals('(11) 9 1234-5678', $result);
    }

    public function testClearCNPJ()
    {
        $result = Strings::clearCNPJ('11.222.333/0001-81');
        $this->assertEquals('11222333000181', $result);
    }

    public function testClearCPF()
    {
        $result = Strings::clearCPF('529.982.247-25');
        $this->assertEquals('52998224725', $result);
    }

    public function testIsCPFValid()
    {
        $this->assertTrue(Strings::isCPF('529.982.247-25'));
    }

    public function testIsCPFInvalid()
    {
        $this->assertFalse(Strings::isCPF('123.456.789-00'));
    }

    public function testIsCNPJValid()
    {
        $this->assertTrue(Strings::isCNPJ('11.222.333/0001-81'));
    }

    public function testPathSanatize()
    {
        $dir = __DIR__ . '/test-dir';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $result = Strings::pathSanatize($dir, true);
        $this->assertEquals(str_replace('\\', '/', $dir), str_replace('\\', '/', $result));
        rmdir($dir);
    }

    public function testEncapsule()
    {
        $result = Strings::encapsule('test', '[', ']');
        $this->assertEquals('[test]', $result);
    }

    public function testCents2Float()
    {
        $result = Strings::cents2float(123456);
        $this->assertEquals('1234.56', $result);
    }

    public function testCents2Moeda()
    {
        $result = Strings::cents2moeda(123456);
        $this->assertEquals('1.234,56', $result);
    }

    public function testClearMoeda()
    {
        $result = Strings::clearMoeda('R$ 1.234,56');
        $this->assertEquals('123456', $result);
    }

    public function testMaskCPF()
    {
        $result = Strings::mask('00100200300', 'cpf');
        $this->assertEquals('001.002.003-00', $result);
    }

    public function testMaskCNPJ()
    {
        $result = Strings::mask('11222333000181', 'cnpj');
        $this->assertEquals('11.222.333/0001-81', $result);
    }

    public function testHumanFilesize()
    {
        $result = Strings::human_filesize(1024);
        $this->assertStringContainsString('1.00', $result);
    }

    public function testGeolocateUf2Estado()
    {
        $result = Strings::geolocate_uf2estado('SP');
        $this->assertEquals('São Paulo', $result);
    }

    public function testGeolocateEstado2Uf()
    {
        $result = Strings::geolocate_estado2uf('São Paulo');
        $this->assertEquals('SP', $result);
    }

    public function testStringFormat()
    {
        $result = Strings::string_format('<b>bold</b>', 'strip_tags');
        $this->assertEquals('bold', $result);
    }

    public function testIntFormatDate()
    {
        $time = strtotime('2024-01-15');
        $result = Strings::int_format($time, 'date');
        $this->assertEquals('15/01/2024', $result);
    }

    public function testShrink()
    {
        $result = Strings::shrink('This is a very long string that needs shrinking', 20);
        $this->assertStringContainsString('...', $result);
    }
}
