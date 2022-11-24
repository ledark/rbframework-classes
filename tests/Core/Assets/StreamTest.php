<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Directory;

class StreamTest extends TestCase {

    public function setUp():void {
        Directory::mkdir( Stream::getCacheAssetsFolder() );
        file_put_contents( Stream::getCacheAssetsFolder() . '/file-sample-created', 'teste' );
    }
    
    public function tearDown():void {
       //Directory::rmdir( Stream::getCacheAssetsFolder() );
    }

    public function testCreationFile() {

        $this->assertFileNotExists(Stream::getCacheAssetsFolder().'/teste.txt');
        $this->assertFileExists(Stream::getCacheAssetsFolder().'/file-sample-created');

        try {
            Stream::filestream('teste.txt');
        } catch (Exception $ex) {
            $this->assertEquals('file teste.txt not exists', $ex->getMessage());
        }
        
        include( Stream::filestream('tests/Samples/app/custom-script.js') );
        
    }
}