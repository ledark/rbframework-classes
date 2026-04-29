<?php

use Framework\Types\Directory;

class DirectoryTest extends \PHPUnit\Framework\TestCase
{
    public function testTrimPath()
    {
        $result = Directory::trimPath('/path/to/dir/');
        $this->assertEquals('path/to/dir', $result);
    }

    public function testTrimPathBackslash()
    {
        $result = Directory::trimPath('\\path\\to\\dir\\');
        $this->assertEquals('path\\to\\dir', $result);
    }

    public function testConstructor()
    {
        $dir = new Directory('path/to/dir');
        $this->assertInstanceOf(Directory::class, $dir);
    }

    public function testGetDirectory()
    {
        $dir = new Directory('path/to/dir');
        $this->assertEquals('path/to/dir', $dir->getDirectory());
    }

    public function testGetDirectoryEmpty()
    {
        $dir = new Directory('');
        $this->assertEquals('./', $dir->getDirectory());
    }

    public function testGetDirectoryWithoutEndSlash()
    {
        $dir = new Directory('path/to/dir/');
        $this->assertEquals('path/to/dir', $dir->getDirectoryWithoutEndSlash());
    }

    public function testGetDirectoryWithEndSlash()
    {
        $dir = new Directory('path/to/dir');
        $result = $dir->getDirectoryWithEndSlash();
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
    }

    public function testIsValidDir()
    {
        $dir = new Directory(__DIR__);
        $this->assertTrue($dir->isValidDir());
    }

    public function testIsValidDirInvalid()
    {
        $dir = new Directory('/invalid/path/that/does/not/exist');
        $this->assertFalse($dir->isValidDir());
    }

    public function testMkdir()
    {
        $path = sys_get_temp_dir() . '/test_dir_' . uniqid();
        Directory::mkdir($path);
        $this->assertTrue(is_dir($path));
        rmdir($path);
    }

    public function testMkdirRecursive()
    {
        $path = sys_get_temp_dir() . '/test_dir_' . uniqid() . '/subdir';
        Directory::mkdir($path);
        $this->assertTrue(is_dir($path));
        rmdir($path);
        rmdir(dirname($path));
    }
}
