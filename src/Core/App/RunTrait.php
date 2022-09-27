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

use RBFrameworks\Core\Session;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Utils\Replace;

trait RunTrait {
    private function end():void {
        $router = $this->getRouter();
        $app = $this;
        $router->set404(function() use ($router, $app) {
            $autoLoader = self::generateTrialsFromPath($router->getCurrentUri(), $router, $app);   
            foreach($autoLoader as $uri) {
                $uri = new File($uri);
                if($uri->hasFile()) {
                    $app->setPage($uri);
                    ob_start();
                    include($this->templatePage->getFilePath());
                    $content = ob_get_clean();
                    $content = new Replace($content, $app->getPageVars());
                    $content->setPattern('([.\w]+)');
                    $content->render();
                    exit();
                }
            }
			if(file_exists('.'.$router->getCurrentUri())) {
                File::readFile('.'.$router->getCurrentUri());
			}
            $app->trigger404();
            exit();
        });
        $router->run();
    }

    //FinalRun
    public function run():void {


        //AutoStart Session
        if($this->getOption('useSession')) new Session();

        //Redirect to [main]
        $this->redirMain();

        //Configure
        $this->prepareRouter();
        $this->prepareTemplate();

        //Handle Need of Mount or NotMount
        if($this->getOption('mount') == '') {

            $this->runRouter();

        } else {

            $this->runRouterMounted();

        }

    }

    //FinalRun: on notMount
    public function runRouter():void {
        $router = $this->getRouter();
        $app = $this;
        if($this->getOption('testMode')) {
            throw new \Exception("runRouter:");
        } 
        if($this->mainFile->hasFile()) include($this->mainFile->getFilePath()); else throw new \Exception($this->mainFile->getOriginalName().' not exists.'); 
        $this->end();
    }

    //FinalRun: on Mount
    private function runRouterMounted() {
        $router = $this->getRouter();
        $app = $this;        
        $router->mount($this->getOption('mount'), function() use ($router, $app) {
            if($this->getOption('testMode')) {
                throw new \Exception("runRouterMounted:".$this->getOption('mount'));
            } 
            if($this->mainFile->hasFile()) include($this->mainFile->getFilePath()); else throw new \Exception($this->mainFile->getOriginalName().' not exists.'); 
        });

        $this->end();
    }    
}