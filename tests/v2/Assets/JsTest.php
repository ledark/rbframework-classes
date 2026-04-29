<?php

use RBFrameworks\Core\Assets\Js;

class JsTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $js = new Js('script.js');
        $this->assertInstanceOf(Js::class, $js);
    }

    public function testGetFile()
    {
        $js = new Js('script.js');
        $result = $js->getFile();
        $this->assertIsString($result);
    }

    public function testSetFile()
    {
        $js = new Js('old.js');
        $js->setFile('new.js');
        $this->assertEquals('new.js', $js->getFile());
    }

    public function testRender()
    {
        $js = new Js('script.js');
        $result = $js->render();
        $this->assertIsString($result);
    }
}
