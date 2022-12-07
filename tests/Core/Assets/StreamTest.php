<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Assets\StreamFile;
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
        ob_clean();
        
    }

    public function testFileReplaces() {
        ob_start();
        include( Stream::filestream('tests/Samples/app/custom-script-2.js', ['varName1' => 'exemplo-1']) );
        $content = ob_get_clean();
        $this->assertStringContainsString('exemplo-1', $content);
    }

    public function testNameFiles() {
        $namesExpected = [
            'sample' => 'sample',
            'path/sample' => 'path_sample',
            'path/another/Sample' => 'path_another_sample',
            'path/Another/sample.txt' => 'path_another_sample-txt',
        ];
        foreach($namesExpected as $name => $expected) {
            $this->assertEquals(StreamFile::getFileNameFrom($name), $expected);
        }
    }
}