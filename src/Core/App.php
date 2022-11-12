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

namespace RBFrameworks\Core;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Types\Directory;
use Bramus\Router\Router;

/**
 * @sample (new App('path/to/routes'))->run();
 * @sample (new App('path/to/routes'))->setOption('useSession', false)->run();
 * Router handle all routes in APP; Special cases are:
 * router hit a page in pages/, then include template
 */

class App {

    use App\RunTrait;
    use App\PageVarsTrait;
    use App\IncludeTrait;
    use App\OptionsTrait;

    /**
     * O Constructor sempre irá requerer o arquivo mainFile, que é onde ele enxergará todas as rotas do Sistema.
     * É Responsabilidade desse arquivo gerenciar as rotas, includes de funções específicas, mas...
     * É Responsabilidade do Framework procurar por:
     * [Pages] e caso existam injetar o arquivo do tamplate.
     */

    public function __construct(string $mainFile, array $appOptions = null, Router $routerService = null) {

        //Create Instances
        $this->router = is_null($routerService) ? new Router() : $routerService;
        $this->router->get('/'.uniqid(), function(){}); //Fix Router Bug
        $this->mainFile = new File($mainFile);

        //Set Default Options
        $this->setOption('mainFile', $this->mainFile->getFilePath());
        
        //Overwrite User Options
        if(!is_null($appOptions)) $this->setOptions($appOptions);

        //Sanitize Some Options
        $this->setOption('baseDir', Types\Directory::trimPath($this->getOption('baseDir')).'/' );
        $this->setOption('pagesDir', Types\Directory::trimPath($this->getOption('pagesDir')).'/' );
        $this->setOption('templatePage', '/'.Types\Directory::trimPath($this->getOption('templatePage')) );      

        $this->checkOptions();

    }

    public function getRouter():Router {
        return $this->router;
    }

    //Overwrite 404
    public function set404(callable $callback = null) {
        $this->on404 = $callback;
    }
    public function trigger404() {
        if(is_callable($this->on404)) call_user_func($this->on404);
    }

    //TemplateFile
    private function prepareTemplate() {
        $this->setTemplatePage( $this->mainFile->getFolderPath().$this->getOption('templatePage'));
    }    
    public function setTemplatePage(string $templatePage):object {
        $this->templatePage = new File($templatePage);
        if(!$this->templatePage->hasFile()) $this->templatePage = new File($this->getOption('baseDir'). $templatePage);
        $this->templatePage->addSearchFolders( $this->mainFile->getSearchFolders() );
        return $this;
    }

    public function prepareRouter() {
        if($this->getOption('setBasePath') != '') {
            $router = $this->getRouter();
            $router->setBasePath($this->getOption('setBasePath'));
        }
    }

    /**
     * isMain: bool if $router has try to access the main
     *
     * @return boolean
     */
    private function isMain():bool {
        $router = $this->getRouter();
        if($router->getCurrentUri() == '' or $router->getCurrentUri() == '/') return true;
        if($router->getCurrentUri() == $this->getOption('mount')) return true;
        if($router->getCurrentUri() == $this->getOption('mount').'/') return true;
        return false;
    }

    private function redirMain() {
        if($this->getOption('main') != '' and $this->getOption('main') != '/' and $this->isMain()) {
            $path = str_replace('//', '/', $this->getOption('mount').$this->getOption('main'));
            $url = Http::getSite().$path;
            $url = substr($url, 0, 10).str_replace('//', '/', substr($url, 10));
            if($this->getOption('testMode')) {
                throw new \Exception("redir:$path");
            }
            Http::redir( $url );
        }
    }

    private static function generateTrialsFromPath(string $path, $router, $app):array {
        return [
            $app->getOption('baseDir').$app->getOption('pagesDir').str_replace($app->getOption('baseDir'),  '', $path),
            $app->getOption('baseDir').$app->getOption('pagesDir').basename($path),
            $app->getOption('baseDir').$app->getOption('pagesDir').$path,
            $app->getOption('baseDir').$app->getOption('mount').$app->getOption('pagesDir').$path,
        ];
    }

    //For User in Template or Pages
    public function setPage(File $page) {
        $this->page = $page;

        //RenderPage onSet
        ob_start();
        $app = $this;

        //AllParts
        if($this->includePagePartPhp('filter')) exit();
        $this->includeRootPartPhp('_filter');
        $this->includeRootPartPhp('_config');
        $this->includePagePartPhp('config');

        if(!$this->includePagePartPhp('head')) $this->includePagePartHtml('head');
        $this->includePagePartCss();
        include($this->page->getFilePath());
        $this->includePagePartJs();
        if(!$this->includePagePartPhp('foot')) $this->includePagePartHtml('foot');
        $this->mainContent = ob_get_clean();
    }
    public function getPage() {
        return $this->page;
    }
    public function renderPage() {
        echo $this->mainContent;
    }

    //Utils
    public function autoFileRouter() {
        $router = $this->getRouter();
        $fileRouterName = $router->getCurrentUri();
        if(substr($fileRouterName, 0, 1) == '/') $fileRouterName = substr($fileRouterName, 1);
        $autoLoader = self::generateTrialsFromPath($fileRouterName, $router, $this);
        array_unshift($autoLoader, $fileRouterName);
        foreach($autoLoader as $fileRouterName) {
            $fileRouter = new File($fileRouterName);
            $fileRouter->clearSearchExtensions();
            $fileRouter->clearSearchFolders();
            $fileRouter->addSearchFolder('./');
            $fileRouter->addSearchExtension('.php');
            if($fileRouter->hasFile()) include($fileRouter->getFilePath());
        }
    }

    public static function loadFolder(string $projectFolder, $mountOn = '') {

        if(!Directory::existsDirectory($projectFolder)) return;
        
            $Directory = Directory::needsDirectory($projectFolder);        

            try {
                $fileIndex = File::needsFiles($Directory->getDirectoryWithoutEndSlash().'/index.php');
            } catch (\Exception $e) {
                $fileIndex = File::needsFiles($Directory->getDirectoryWithoutEndSlash().'/main.php');
            } catch (\Exception $e) {
                $fileIndex = File::needsFiles($Directory->getDirectoryWithoutEndSlash().'/app.php');
            } catch (\Exception $e) {
                
            } finally {

                if(isset($fileIndex)) {
                    include($fileIndex->getFilePath());
                }

            }

    }

    public static function handleRequestUri(string $when, string $appFolder) {
        $uri = Http::getRequestUri();
        if(substr($uri, 0, strlen($when)) == $when) {
            self::loadFolder($appFolder, $when);
        }
        if(strpos(($uri), $when) !== false) {
            self::loadFolder($appFolder, $when);
        }
    }

    public static function handleMultipleRequestUris(array $requestUris) {
        foreach($requestUris as $requestPattern => $requestUri) {
            self::handleRequestUri($requestPattern, $requestUri);
        }
    }

}