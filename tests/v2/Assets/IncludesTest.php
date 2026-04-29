<?php

use RBFrameworks\Core\Assets\Includes;

class IncludesTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $includes = new Includes();
        $this->assertInstanceOf(Includes::class, $includes);
    }

    public function testRender()
    {
        $includes = new Includes();
        $result = $includes->render();
        $this->assertIsString($result);
    }
}
