<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Types\HtmlElement;

class HtmlElementTest extends TestCase {

    public function testWrapElement() {
        $element = new HtmlElement('teste', 'div', ['id' => "row"]);
        $expected = '<div id="row">teste</div>';
        $this->assertEquals($expected, $element->getFormatted());

        $element->addChildElement('div', ['id' => "col"]);
        $expected = '<div id="row"><div id="col">teste</div></div>';
        $this->assertEquals($expected, $element->getFormatted());

        $element->wrapElement('div', ['id' => "container"]);
        $this->assertEquals("<div id=\"container\">{$expected}</div>", $element->getFormatted());

    }
    
    public function testCloseTags() {
        $element = new HtmlElement('teste');
        $this->assertEquals('<span>teste</span>', $element->getFormatted());

        $element = new HtmlElement('');
        $this->assertEquals('<span></span>', $element->getFormatted());

        $element = new HtmlElement();
        $this->assertEquals('<span/>', $element->getFormatted());
        
        $element = new HtmlElement(null, 'img', ['src' => 'example']);
        $this->assertEquals('<img src="example"/>', $element->getFormatted());

    }

    public function testAttributes() {
        $element = new HtmlElement('teste', 'span', ['class' => 'teste']);
        $this->assertEquals('<span class="teste">teste</span>', $element->getFormatted());
        
        $element = new HtmlElement('teste', 'div', ['class' => 'teste', 'another' => 'a', 'number' => 123]);
        $this->assertEquals('<div class="teste" another="a" number="123">teste</div>', $element->getFormatted());
    }

}