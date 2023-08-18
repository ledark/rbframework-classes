<?php

use PHPUnit\Framework\TestCase;

use RBFrameworks\Core\Directory;

class CreateTest extends TestCase {

    public function testAssertMkdir() {

        //Gerar Diretório Aleatório
        $src = '';
        for($i = 0; $i <rand(1,12); $i++) {
            $src.= '/'.uniqid();
        }
        
        //Não Existe Nada no Início
        $this->assertFileNotExists(__DIR__.$src);

        //Agora deve Existir
        Directory::mkdir(__DIR__.$src);
        $this->assertFileExists(__DIR__.$src);

        //Agora Pode Remover
        Directory::rmdir(__DIR__.$src);
        $this->assertFileNotExists(__DIR__.$src);
    }

}

