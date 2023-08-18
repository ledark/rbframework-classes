<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Assets\Js;

class JSTest extends TestCase {
    public function testJS() {
        
        $this->assertEquals('<script src="teste" type="text/javascript" ></script>', Js::getTagNormal('teste'));
        $this->assertEquals('<script src="teste" type="module" ></script>', Js::getTagModule('teste'));
        $this->assertEquals('<script src="teste" type="impossible" ></script>', Js::getTag('teste', 'impossible'));
        
    }
}