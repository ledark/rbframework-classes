<?php

use RBFrameworks\Core\Assets\Vue\FastRender;

class FastRenderTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $fastRender = new FastRender();
        $this->assertInstanceOf(FastRender::class, $fastRender);
    }
}
