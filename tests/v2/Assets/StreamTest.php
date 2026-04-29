<?php

use RBFrameworks\Core\Assets\Stream;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $stream = new Stream();
        $this->assertInstanceOf(Stream::class, $stream);
    }
}
