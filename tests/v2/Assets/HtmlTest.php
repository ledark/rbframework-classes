<?php

use RBFrameworks\Core\Assets\Html;

class HtmlTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $html = new Html('index.html');
        $this->assertInstanceOf(Html::class, $html);
    }

    public function testGetFile()
    {
        $html = new Html('index.html');
        $result = $html->getFile();
        $this->assertIsString($result);
    }

    public function testSetFile()
    {
        $html = new Html('old.html');
        $html->setFile('new.html');
        $this->assertEquals('new.html', $html->getFile());
    }

    public function testRender()
    {
        $html = new Html('index.html');
        $result = $html->render();
        $this->assertIsString($result);
    }
}
