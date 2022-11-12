<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Config;

class AssetsTest extends TestCase
{

    public function setUp() {
        if(is_dir(Config::get('location.cache.assets'))) {
            foreach (new DirectoryIterator(Config::get('location.cache.assets')) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                unlink($fileInfo->getPathname());
            }
            Directory::rmdir(Config::get('location.cache.assets'));
        }
    }

    public function tearDown() {
        if(is_dir(Config::get('location.cache.assets'))) {
            foreach (new DirectoryIterator(Config::get('location.cache.assets')) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                unlink($fileInfo->getPathname());
            }
            Directory::rmdir(Config::get('location.cache.assets'));
        }     
    }

    public function testAssets()
    {

        
        

        $this->assertFileNotExists(Config::get('location.cache.assets'));


        $filename = Stream::filestream(__DIR__.'/../../tests/Samples/app/custom-style.css');

        $this->assertFileExists($filename);
        $this->assertFileExists(Config::get('location.cache.assets'));

        unlink($filename);

        //Directory::rmdir(Config::get('location.cache.assets'));

        ;
    }

}