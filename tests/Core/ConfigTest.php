<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Config;

class ConfigTest extends TestCase
{
    public function test_getCollectionDir() {
        $x = Config::getCollectionDir();
        $this->assertDirectoryExists($x);
    }

    public function test_getCollectionNames() {
        $x = Config::getCollectionNames();
        foreach (new DirectoryIterator("collections") as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $name = $fileInfo->getFilename();
            $this->assertContains(basename($name, '.php'), $x);
        }        

        $this->assertEquals('', '');
    }

}