<?php 

function get_collection_path():string {
    return __DIR__.'/collection/';
}

class ConfigTest extends \PHPUnit\Framework\TestCase {
    public function testConfig() {
        $config = new \Framework\Config();
        $this->assertInstanceOf(\Framework\Config::class, $config);
        $this->assertNull(\Framework\Config::get('invalid_dict'));
        $this->assertEquals('ninguno', \Framework\Config::get('invalid_dict', 'ninguno'));
        $this->assertTrue(\Framework\Config::get('dict.setting', 'ninguno'));


        $this->assertEquals('valor da opcao1', \Framework\Config::get('dict.opcao1')());
        $this->assertIsArray(\Framework\Config::get('dict.cor'));

        $this->assertArrayHasKey('azul', \Framework\Config::get('dict.cor'));
        $this->assertArrayHasKey('verde', \Framework\Config::get('dict.cor'));
        $this->assertArrayHasKey('vermelha', \Framework\Config::get('dict.cor'));
        $this->assertEquals('fria', \Framework\Config::get('dict.cor.azul'));
        $this->assertEquals('fria', \Framework\Config::get('dict.cor.verde'));
        $this->assertEquals('quente', \Framework\Config::get('dict.cor.vermelha'));
        $this->assertEquals('azedo', \Framework\Config::get('dict.fruta.citrica.limao'));


        /*
        $this->assertEquals('true', \Framework\Config::get('setting'));
        $this->assertEquals('fria', \Framework\Config::get('cor.azul'));
        $this->assertEquals('fria', \Framework\Config::get('cor.verde'));
        $this->assertEquals('quente', \Framework\Config::get('cor.vermelha'));
        $this->assertEquals('array', \Framework\Config::get('fruta.citrica'));
        $this->assertEquals('azedo', \Framework\Config::get('fruta.citrica.limao'));
        $this->assertEquals('doce', \Framework\Config::get('fruta.citrica.laranja'));
        $this->assertEquals('melao', \Framework\Config::get('fruta.amarga'));
        */

    }

}