<?php

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Types\Form\Input;
use RBFrameworks\Core\Types\Form\InputButton;

class InputTest extends TestCase {
    
        public function testInput() {
            $input = new Input('teste');
            $this->assertEquals('<input id="teste" name="teste" type="text"/>', $input->getFormatted());
        }
    
        /*
        public function testInputButton() {
            $input = new InputButton('teste');
            $this->assertEquals('<input id="teste" name="teste" type="button"/>', $input->getFormatted());
        }
        */
    
}