<?php

use Framework\Types\File;

class FileTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $file = new File('test.txt');
        $this->assertInstanceOf(File::class, $file);
    }

    public function testGetOriginalName()
    {
        $file = new File('path/to/test.txt');
        $this->assertEquals('path/to/test.txt', $file->getOriginalName());
    }

    public function testGetName()
    {
        $file = new File('path/to/test.txt');
        $this->assertEquals('test.txt', $file->getName());
    }

    public function testAddSearchFolder()
    {
        $file = new File('test.txt');
        $file->addSearchFolder('/custom/path/');
        $this->assertContains('/custom/path/', $file->getSearchFolders());
    }

    public function testAddSearchExtension()
    {
        $file = new File('test');
        $file->addSearchExtension('.txt');
        $this->assertContains('.txt', $file->getSearchExtensions());
    }

    public function testAddSearchPrefix()
    {
        $file = new File('test.txt');
        $file->addSearchPrefix('prefix_');
        $this->assertContains('prefix_', $file->getSearchPrefixes());
    }

    public function testClearSearchFolders()
    {
        $file = new File('test.txt');
        $file->clearSearchFolders();
        $this->assertEmpty($file->getSearchFolders());
    }

    public function testClearSearchExtensions()
    {
        $file = new File('test.txt');
        $file->clearSearchExtensions();
        $this->assertEmpty($file->getSearchExtensions());
    }

    public function testClearSearchPrefixes()
    {
        $file = new File('test.txt');
        $file->clearSearchPrefixes();
        $this->assertEmpty($file->getSearchPrefixes());
    }

    public function testIsDir()
    {
        $file = new File('test.txt');
        $this->assertFalse($file->isDir());
    }

    public function testHasFile()
    {
        $file = new File(__FILE__);
        $this->assertTrue($file->hasFile());
    }

    public function testHasFileNotFound()
    {
        $file = new File('nonexistent_file_12345.txt');
        $this->assertFalse($file->hasFile());
    }

    public function testGetFilePath()
    {
        $file = new File(__FILE__);
        $this->assertEquals(str_replace('\\', '/', __FILE__), str_replace('\\', '/', $file->getFilePath()));
    }

    public function testGetExtension()
    {
        $file = new File('test.txt');
        $file->addSearchFolder(__DIR__ . '/');
        $file->addSearchExtension('.php');
        $result = $file->getExtension();
        $this->assertIsString($result);
    }

    public function testToString()
    {
        $file = new File(__FILE__);
        $this->assertEquals(str_replace('\\', '/', __FILE__), str_replace('\\', '/', (string) $file));
    }

    public function testGetFileContents()
    {
        $file = new File(__FILE__);
        $contents = $file->getFileContents();
        $this->assertIsString($contents);
    }

    public function testNeedsFiles()
    {
        $file = File::needsFiles(__FILE__);
        $this->assertInstanceOf(File::class, $file);
    }

    public function testNeedsFilesNotFound()
    {
        $this->expectException(\Exception::class);
        File::needsFiles('nonexistent_file_12345.txt');
    }

    public function testExistsFile()
    {
        $this->assertTrue(File::existsFile(__FILE__));
    }

    public function testGetFileExtension()
    {
        $result = File::getFileExtension('test.txt');
        $this->assertEquals('txt', $result);
    }

    public function testGetMimeType()
    {
        $result = File::getMimeType(__FILE__);
        $this->assertIsString($result);
    }

    public function testPreferInclude()
    {
        $file = new File(__FILE__);
        $file->preferInclude(true);
        $this->assertInstanceOf(File::class, $file);
    }
}
