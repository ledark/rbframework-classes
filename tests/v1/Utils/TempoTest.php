<?php

use Framework\Utils\Tempo;

class TempoTest extends \PHPUnit\Framework\TestCase
{
    public function testHtconvertTimestr2Timefrac()
    {
        $result = Tempo::htconvert_timestr2timefrac('01:30:00');
        $this->assertEquals('1.75', $result);
    }

    public function testHtconvertTimestr2TimefracWithSeconds()
    {
        $result = Tempo::htconvert_timestr2timefrac('01:30:45');
        $this->assertEquals('1.75', $result);
    }

    public function testHtconvertTimestr2Timehuman()
    {
        $result = Tempo::htconvert_timestr2timehuman('01:30:00');
        $this->assertEquals('1:30', $result);
    }

    public function testDateDecodeBR()
    {
        $result = Tempo::date_decode('15/01/2024');
        $this->assertEquals('br', $result);
    }

    public function testDateDecodeEN()
    {
        $result = Tempo::date_decode('2024-01-15');
        $this->assertEquals('en', $result);
    }

    public function testDateDecodeUnix()
    {
        $result = Tempo::date_decode('1705276800');
        $this->assertEquals('unix', $result);
    }

    public function testDateConvertBR2EN()
    {
        $result = Tempo::date_convert_br2en('15/01/2024');
        $this->assertEquals('2024-01-15', $result);
    }

    public function testDateConvertEN2BR()
    {
        $result = Tempo::date_convert_en2br('2024-01-15');
        $this->assertEquals('15/01/2024', $result);
    }

    public function testDateConvertUnix2BR()
    {
        $unix = strtotime('2024-01-15');
        $result = Tempo::date_convert_unix2br($unix);
        $this->assertEquals('15/01/2024', $result);
    }

    public function testDateConvertUnix2EN()
    {
        $unix = strtotime('2024-01-15');
        $result = Tempo::date_convert_unix2en($unix);
        $this->assertEquals('2024-01-15', $result);
    }

    public function testDateConvert()
    {
        $result = Tempo::date_convert('15/01/2024', 'en');
        $this->assertEquals('2024-01-15', $result);
    }

    public function testDateUnixpossible()
    {
        $result = Tempo::date_unixpossible('15/01/2024');
        $this->assertTrue($result);
    }

    public function testDateFormatarYear()
    {
        $result = Tempo::date_formatar('15/01/2024', 'Y');
        $this->assertEquals('2024', $result);
    }

    public function testDateFormatarMonth()
    {
        $result = Tempo::date_formatar('15/01/2024', 'm');
        $this->assertEquals('01', $result);
    }

    public function testDateFormatarDay()
    {
        $result = Tempo::date_formatar('15/01/2024', 'd');
        $this->assertEquals('15', $result);
    }

    public function testDateFormatarWeekday()
    {
        $result = Tempo::date_formatar('15/01/2024', 'semana');
        $this->assertIsString($result);
    }

    public function testDateFormatarMonthName()
    {
        $result = Tempo::date_formatar('15/01/2024', 'mes');
        $this->assertEquals('Janeiro', $result);
    }

    public function testTempo()
    {
        $result = Tempo::Tempo(3660);
        $this->assertStringContainsString('hora', $result);
    }
}
