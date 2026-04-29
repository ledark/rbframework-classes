<?php

use RBFrameworks\Core\Assets\StreamFile;

class StreamFileTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $streamFile = new StreamFile(__DIR__ . '/test-file.txt');
        $this->assertInstanceOf(StreamFile::class, $streamFile);
    }
}
