<?php

use RBFrameworks\Core\Imagem;

class ImagemTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorWithPath()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertInstanceOf(Imagem::class, $imagem);
    }

    public function testGetImagemOriginal()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertEquals(__FILE__, $imagem->getImagemOriginal());
    }

    public function testSetOriginalPath()
    {
        $imagem = new Imagem('old.jpg');
        $result = $imagem->setOriginalPath('new.jpg');
        $this->assertInstanceOf(Imagem::class, $result);
        $this->assertEquals('new.jpg', $imagem->getImagemOriginal());
    }

    public function testGetDimensions()
    {
        $imagem = new Imagem(__FILE__);
        $dimensions = $imagem->getDimensions();
        $this->assertIsArray($dimensions);
        $this->assertArrayHasKey('width', $dimensions);
        $this->assertArrayHasKey('height', $dimensions);
    }

    public function testSetDimensions()
    {
        $imagem = new Imagem(__FILE__);
        $imagem->setDimensions(100, 200, 100, 'crop');
        $dimensions = $imagem->getDimensions();
        $this->assertEquals(100, $dimensions['width']);
        $this->assertEquals(200, $dimensions['height']);
    }

    public function testIsWebp()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertIsBool($imagem->isWebp());
    }

    public function testIsCacheEnabled()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertIsBool($imagem->isCacheEnabled());
    }

    public function testIsRemote()
    {
        $imagem = new Imagem(__FILE__);
        $this->assertIsBool($imagem->isRemote());
    }

    public function testGetCachedFilename()
    {
        $imagem = new Imagem(__FILE__);
        $filename = $imagem->getCachedFilename();
        $this->assertIsString($filename);
    }
}
