<?php

use RBFrameworks\Core\Assets\Vue;

class VueTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $vue = new Vue('app.js');
        $this->assertInstanceOf(Vue::class, $vue);
    }

    public function testGetFile()
    {
        $vue = new Vue('app.js');
        $result = $vue->getFile();
        $this->assertIsString($result);
    }

    public function testRender()
    {
        $vue = new Vue('app.js');
        $result = $vue->render();
        $this->assertIsString($result);
    }
}
