<?php

use RBFrameworks\Core\Imagem;

class ImagemTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorWithPath()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertInstanceOf(Imagem::class, $imagem);
    }

    public function testConstructorWithoutPath()
    {
        $imagem = new Imagem();
        $this->assertInstanceOf(Imagem::class, $imagem);
    }

    public function testGetPath()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertEquals(__FILE__, $imagem->getPath());
    }

    public function testSetPath()
    {
        $imagem = new Imagem('old.jpg');
        $imagem->setPath('new.jpg');
        $this->assertEquals('new.jpg', $imagem->getPath());
    }

    public function testGetWidth()
    {
        $imagem = new Imagem();
        $this->assertIsInt($imagem->getWidth());
    }

    public function testGetHeight()
    {
        $imagem = new Imagem();
        $this->assertIsInt($imagem->getHeight());
    }

    public function testGetMime()
    {
        $imagem = new Imagem();
        $this->assertIsString($imagem->getMime());
    }

    public function testIsValid()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertIsBool($imagem->isValid());
    }

    public function testResize()
    {
        $imagem = new Imagem();
        $result = $imagem->resize(100, 100);
        $this->assertInstanceOf(Imagem::class, $result);
    }

    public function testCrop()
    {
        $imagem = new Imagem();
        $result = $imagem->crop(50, 50);
        $this->assertInstanceOf(Imagem::class, $result);
    }

    public function testSave()
    {
        $imagem = new Imagem();
        $result = $imagem->save('test_output.jpg');
        $this->assertIsBool($result);
    }
}
