<?php

/*
Copyright (c) 2022 Ricardo Bermejo
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Ricardo Bermejo" <ricardo@bermejo.com.br>
 * @package  App
 * @version  1.0
 * @license  Revised BSD
  */
  
namespace RBFrameworks\Core\App;

use RBFrameworks\Core\Exceptions\AppException as Exception;

trait OptionsTrait {

    public $mainFile = null;
    public $templatePage;
    public $page;
    public $appOptions = [
        
        //BaseMount for All Routes
        'mount' => '/',

        //Arquivo que sempre será incluído, acima de Template and Page
        'mainFile' => 'route.php',
        
        //Arquivo Padrão para a Rota Inicial em /
        'main' => '',

        //Arquivo Padrão para executar o Template quando a página existir
        'templatePage' => '/index.html',
        
        //Prefixo de Diretório para encontrar o APP
        'baseDir' => '',

        //Diretório padrão onde será encontrado as Pages
        'pagesDir' => 'pages/',

        //AutoStart Session using Core\Session
        'useSession' => true,

        //Override Bramus/Router BasePath
        'setBasePath' => '',

        //Define Result as Testable
        'testMode' => false,
    ];

    //Variaveis para Substituicao onRender
    public $pageVars = [];

    private $router = null;
    private $on404 = null;

    //RenderPage
    private $mainContent = "";


    private function checkOptions() {

        if(strpos($this->getOption('mount'), '/') !== 0) Exception::throw('O mount é a base da url para o app e deve começar com /');

    }

    //Getters and Setters
    public function setOption(string $key, $value):object {
        $this->appOptions[$key] = $value;
        return $this;
    }

    private function setOptions(array $options):object {
        $this->appOptions = array_merge($this->getOptions(), $options);
        return $this;
    }
    
    private function getOptions():array {
        return $this->appOptions;
    }

    public function getOption(string $key) {
        if(isset($this->getOptions()[$key])) return $this->getOptions()[$key];
        throw new \Exception("$key is a Invalid Option");
    }    

}