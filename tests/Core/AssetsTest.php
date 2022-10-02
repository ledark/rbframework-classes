<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Config;

class AssetsTest extends TestCase
{

    public function testAssets()
    {

        $this->assertFileNotExists(Config::get('location.cache.assets'));


        $filename = Stream::filestream(__DIR__.'/../../tests/Samples/app/custom-style.css');

        $this->assertFileExists($filename);
        $this->assertFileExists(Config::get('location.cache.assets'));

        unlink($filename);

        Directory::rmdir(Config::get('location.cache.assets'));

        ;
    }

}