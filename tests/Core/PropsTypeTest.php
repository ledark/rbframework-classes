<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Types\PropBoolean;
use RBFrameworks\Core\Types\PropMixed;
use RBFrameworks\Core\Types\PropNumber;
use RBFrameworks\Core\Types\PropProps;
use RBFrameworks\Core\Types\PropString;
use RBFrameworks\Core\Config;

class PropsTypeTest extends TestCase {

    public function testDeclarePropBoolean(){
        $props = new PropBoolean('propName');
        $this->assertArrayHasKey('propName', $props->getFormatted());
        $this->assertIsBool($props->getValue());
        $this->assertFalse($props->getValue());
    }

    public function testDeclarePropMixed(){
        $props = new PropMixed('propName');
        $this->assertArrayHasKey('propName', $props->getFormatted());
        $this->assertNull($props->getValue());
        $this->assertIsNotString($props->getValue());
    }

    public function testDeclarePropNumber(){
        $props = new PropNumber('propName');
        $this->assertArrayHasKey('propName', $props->getFormatted());
        $this->assertIsNumeric($props->getValue());
        $this->assertEquals($props->getValue(), -1);
    }

    public function testDeclarePropString(){
        $props = new PropString('propName');
        $this->assertArrayHasKey('propName', $props->getFormatted());
        $this->assertIsString($props->getValue());
        $this->assertEquals($props->getValue(), '');
    }

    public function testDeclarePropProps(){
        $props = new PropProps('myPropNameGroup', ['cor' => 'azul', 'size' => 'M', 'age' => 18]);
        $this->assertArrayHasKey('myPropNameGroup', $props->getFormatted());
        $this->assertIsArray($props->getValue());
        $this->assertEquals($props->getValue(), ['cor' => 'azul', 'size' => 'M', 'age' => 18]);
    }    

    public function testValuePropBoolean(){
        $props = new PropBoolean('propName', 'true');
        $this->assertEquals($props->getValue(), true);
    }

    public function testValuePropMixed(){
        $props = new PropMixed('propName', 'false');
        $this->assertEquals($props->getValue(), 'false');
    }

    public function testValuePropNumber(){
        $props = new PropNumber('propName', '123');
        $this->assertEquals($props->getValue(), 123);
    }

    public function testValuePropString(){
        $props = new PropString('propName', 'teste');
        $this->assertEquals($props->getValue(), 'teste');
    }

    public function testValuePropProps(){
        $props = new PropProps('myPropNameGroup', ['cor' => 'azul', 'size' => 'M', 'age' => 18]);
        $this->assertEquals($props->getValue(), ['cor' => 'azul', 'size' => 'M', 'age' => 18]);
    }    

}