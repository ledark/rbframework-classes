<?php

use RBFrameworks\Core\Assets\Css;

class CssTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $css = new Css('style.css');
        $this->assertInstanceOf(Css::class, $css);
    }

    public function testGetFile()
    {
        $css = new Css('style.css');
        $result = $css->getFile();
        $this->assertIsString($result);
    }

    public function testSetFile()
    {
        $css = new Css('old.css');
        $css->setFile('new.css');
        $this->assertEquals('new.css', $css->getFile());
    }

    public function testRender()
    {
        $css = new Css('style.css');
        $result = $css->render();
        $this->assertIsString($result);
    }

    public function testRenderInline()
    {
        $css = new Css('style.css');
        $result = $css->render(true);
        $this->assertIsString($result);
    }
}
