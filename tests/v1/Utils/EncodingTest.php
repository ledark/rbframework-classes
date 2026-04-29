<?php

use Framework\Utils\Encoding;

class EncodingTest extends \PHPUnit\Framework\TestCase
{
    public function testToUTF8FromISO()
    {
        $isoString = iconv('UTF-8', 'ISO-8859-1', 'Test String');
        $result = Encoding::toUTF8($isoString);
        $this->assertIsString($result);
    }

    public function testToUTF8AlreadyUTF8()
    {
        $string = 'Test String';
        $result = Encoding::toUTF8($string);
        $this->assertEquals('Test String', $result);
    }

    public function testToWin1252()
    {
        $string = 'Test String';
        $result = Encoding::toWin1252($string);
        $this->assertIsString($result);
    }

    public function testToISO8859()
    {
        $string = 'Test String';
        $result = Encoding::toISO8859($string);
        $this->assertIsString($result);
    }

    public function testToLatin1()
    {
        $string = 'Test String';
        $result = Encoding::toLatin1($string);
        $this->assertIsString($result);
    }

    public function testFixUTF8()
    {
        $string = 'Test String';
        $result = Encoding::fixUTF8($string);
        $this->assertIsString($result);
    }

    public function testUTF8FixWin1252Chars()
    {
        $string = "Test\x80String";
        $result = Encoding::UTF8FixWin1252Chars($string);
        $this->assertIsString($result);
    }

    public function testRemoveBOM()
    {
        $string = pack("CCC", 0xef, 0xbb, 0xbf) . 'Test';
        $result = Encoding::removeBOM($string);
        $this->assertEquals('Test', $result);
    }

    public function testHasUTF8()
    {
        $string = 'Test String';
        $result = Encoding::has_utf8($string);
        $this->assertIsBool($result);
    }

    public function testNormalizeEncoding()
    {
        $result = Encoding::normalizeEncoding('utf8');
        $this->assertEquals('UTF-8', $result);
    }

    public function testNormalizeEncodingISO()
    {
        $result = Encoding::normalizeEncoding('iso88591');
        $this->assertEquals('ISO-8859-1', $result);
    }

    public function testEncode()
    {
        $result = Encoding::encode('UTF-8', 'Test String');
        $this->assertIsString($result);
    }

    public function testDeepEncode()
    {
        $input = 'Test String';
        Encoding::DeepEncode($input);
        $this->assertIsString($input);
    }

    public function testDeepDecode()
    {
        $input = 'Test String';
        Encoding::DeepDecode($input);
        $this->assertIsString($input);
    }

    public function testDetect()
    {
        $result = Encoding::detect('Test String');
        $this->assertIsString($result);
    }
}
