<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Types\Dimension;

class DimensionTypeTest extends TestCase {

    public function testDefaults() {
        $dimension = new Dimension(170);
        $this->assertEquals($dimension->getUn(), 'cm');

        $dimension->asMm();
        $this->assertEquals($dimension->getUn(), 'mm');

        $this->assertEquals($dimension->getCentimeter(), 17);
    }

    public function testMm() {
        $dimension = new Dimension('10mm');
        $this->assertEquals($dimension->getUn(), 'mm');
        $this->assertEquals($dimension->getNumber(), 10);
        $this->assertEquals($dimension->getFormatted(), '10mm');
        $this->assertEquals($dimension->getFormatted(true), '10 mm');

        $this->assertGreaterThanOrEqual($dimension->getMilimeter(),     10         , "{$dimension->getFormatted()} to Milimeter:  {$dimension->getConverted('mm')}mm         " );
        $this->assertGreaterThanOrEqual($dimension->getCentimeter(),    1          , "{$dimension->getFormatted()} to Centimeter: {$dimension->getConverted('cm')}cm         " );
        $this->assertGreaterThanOrEqual($dimension->getMeter(),         0.01       , "{$dimension->getFormatted()} to Meter:      {$dimension->getConverted('m')}m           " );
        $this->assertGreaterThanOrEqual($dimension->getKilometer(),     1e-5       , "{$dimension->getFormatted()} to Kilometer:  {$dimension->getConverted('km')}km         " );

    }

    public function testCm() {
        $dimension = new Dimension('10 cm');
        $this->assertEquals($dimension->getUn(), 'cm');
        $this->assertEquals($dimension->getNumber(), 10);
        $this->assertEquals($dimension->getFormatted(), '10cm');
        $this->assertEquals($dimension->getFormatted(true), '10 cm');

        $this->assertGreaterThanOrEqual($dimension->getMilimeter(),     100         , "{$dimension->getFloat()} | {$dimension->getFormatted()} to Milimeter:  {$dimension->getConverted('mm')}mm         " );
        $this->assertGreaterThanOrEqual($dimension->getCentimeter(),    10           , "{$dimension->getFloat()} | {$dimension->getFormatted()} to Centimeter: {$dimension->getConverted('cm')}cm         " );
        $this->assertGreaterThanOrEqual($dimension->getMeter(),         0.1         , "{$dimension->getFloat()} | {$dimension->getFormatted()} to Meter:      {$dimension->getConverted('m')}m           " );
        $this->assertGreaterThanOrEqual($dimension->getKilometer(),     1e-4        , "{$dimension->getFloat()} | {$dimension->getFormatted()} to Kilometer:  {$dimension->getConverted('km')}km         " );        
    }

    public function testM() {
        $dimension = new Dimension('10 m');
        $this->assertEquals($dimension->getUn(), 'm');
        $this->assertEquals($dimension->getNumber(), 10);
        $this->assertEquals($dimension->getFormatted(), '10m');
        $this->assertEquals($dimension->getFormatted(true), '10 m');

        $this->assertGreaterThanOrEqual($dimension->getMilimeter(),     10000         , "{$dimension->getFormatted()} to Milimeter:  {$dimension->getConverted('mm')}mm         " );
        $this->assertGreaterThanOrEqual($dimension->getCentimeter(),    1000          , "{$dimension->getFormatted()} to Centimeter: {$dimension->getConverted('cm')}cm         " );
        $this->assertGreaterThanOrEqual($dimension->getMeter(),         10       , "{$dimension->getFormatted()} to Meter:      {$dimension->getConverted('m')}m           " );
        $this->assertGreaterThanOrEqual($dimension->getKilometer(),     0.01       , "{$dimension->getFormatted()} to Kilometer:  {$dimension->getConverted('km')}km         " );        
    }

    public function testKm() {
        $dimension = new Dimension('10 km');
        $this->assertEquals($dimension->getUn(), 'km');
        $this->assertEquals($dimension->getNumber(), 10);
        $this->assertEquals($dimension->getFormatted(), '10km');
        $this->assertEquals($dimension->getFormatted(true), '10 km');

        $this->assertGreaterThanOrEqual($dimension->getMilimeter(),     1e+7         , "{$dimension->getFormatted()} to Milimeter:  {$dimension->getConverted('mm')}mm         " );
        $this->assertGreaterThanOrEqual($dimension->getCentimeter(),    1e+6          , "{$dimension->getFormatted()} to Centimeter: {$dimension->getConverted('cm')}cm         " );
        $this->assertGreaterThanOrEqual($dimension->getMeter(),         10000       , "{$dimension->getFormatted()} to Meter:      {$dimension->getConverted('m')}m           " );
        $this->assertGreaterThanOrEqual($dimension->getKilometer(),     10       , "{$dimension->getFormatted()} to Kilometer:  {$dimension->getConverted('km')}km         " );        
    }

}