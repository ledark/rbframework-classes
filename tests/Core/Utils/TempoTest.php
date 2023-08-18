<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Tempo;

class TempoTest extends TestCase
{
    public function test_getCollectionNames() {
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:12:00'), '0.25');
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:22:00'), '0.50');
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:29:00'), '0.50');
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:30:00'), '0.75');
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:44:00'), '0.75');
        $this->assertEquals(Tempo::htconvert_timestr2timefrac('00:47:00'), '1.00');
    }
    
    public function testdate_decode() {
        $this->assertEquals(Tempo::date_decode('14/03/1986'), 'br');
        $this->assertEquals(Tempo::date_decode('1986-13-14'), 'en');
        $this->assertEquals(Tempo::date_decode(time()), 'unix');
    }

}