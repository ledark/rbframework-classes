<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Session;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Types\Directory;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Http;
use RBFrameworks\Core\App\IncludeTrait;
use Bramus\Router\Router;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Exceptions\AppException as Exception;

/**
 * @sample (new App('path/to/routes'))->run();
 * @sample (new App('path/to/routes'))->setOption('useSession', false)->run();
 * Router handle all routes in APP; Special cases are:
 * router hit a page in pages/, then include template
 */

class App {

    public $mainFile = '';
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

    /**
     * O Constructor sempre irá requerer o arquivo mainFile, que é onde ele enxergará todas as rotas do Sistema.
     * É Responsabilidade desse arquivo gerenciar as rotas, includes de funções específicas, mas...
     * É Responsabilidade do Framework procurar por:
     * [Pages] e caso existam injetar o arquivo do tamplate.
     */

    public function __construct(string $mainFile, array $appOptions = null, Router $routerService = null) {

        //Create Instances
        $this->router = is_null($routerService) ? new Router() : $routerService;
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

    private function checkOptions():void {

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

    public function getRouter():Router {
        return $this->router;
    }

    private function getOptions():array {
        return $this->appOptions;
    }

    public function getOption(string $key) {
        if(isset($this->getOptions()[$key])) return $this->getOptions()[$key];
        throw new \Exception("$key is a Invalid Option");
    }

    //Overwrite 404
    public function set404(callable $callback = null) {
        $this->on404 = $callback;
    }
    public function trigger404() {
        if(is_callable($this->on404)) call_user_func($this->on404);
    }

    //TemplateFile
    private function prepareTemplate():void {
        $this->setTemplatePage( $this->mainFile->getFolderPath().$this->getOption('templatePage'));
    }    
    public function setTemplatePage(string $templatePage):object {
        $this->templatePage = new File($templatePage);
        if(!$this->templatePage->hasFile()) $this->templatePage = new File($this->getOption('baseDir'). $templatePage);
        $this->templatePage->addSearchFolders( $this->mainFile->getSearchFolders() );
        return $this;
    }

    public function prepareRouter():void {
        if($this->getOption('setBasePath') != '') {
            $router = $this->getRouter();
            $router->setBasePath($this->getOption('setBasePath'));
        }
    }

    //PageVars
    public function addPageVar(string $key, string $value): object {
        $this->pageVars[$key] = $value;
        return $this;
    }

    public function addPageVars(array $pageVars, bool $overwrite = false): object {
        return $this->setPageVars($pageVars, $overwrite);
    }

    public function setPageVars(array $pageVars, bool $overwrite = true): object {
        foreach($pageVars as $key => $value) {
            if(isset($this->pageVars[$key]) and $overwrite == false) {
                continue;
            }
            if(is_string($value)) {
                $this->addPageVar($key, $value);
                continue;
            }
            if(is_array($value) and !Utils\Arrays::isAssoc($value)) {
                $this->addPageVar($key, implode(', ', $value));
                continue;
            }
            if(is_array($value) and Utils\Arrays::isAssoc($value)) {
                $newPageVars = [];
                foreach($value as $k => $v) {
                    $newPageVars[$key.'.'.$k] = $v;
                }
                $this->setPageVars($newPageVars, $overwrite);
            }
        }
        return $this;
    }

    public function getPageVars(): array {
        return $this->pageVars;
    }

    public function getPageVar(string $key): string {
         return isset($this->pageVars[$key]) ? $this->pageVars[$key] : "";
    }

    public function renderPageVar(string $key): void {
        echo $this->getPageVar($key);
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

    private function redirMain():void {
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

    private static function generateTrialsFromPath(string $path, $router, $app):array {
        return [
            $app->getOption('baseDir').$app->getOption('pagesDir').str_replace($app->getOption('baseDir'),  '', $path),
            $app->getOption('baseDir').$app->getOption('pagesDir').basename($path),
            $app->getOption('baseDir').$app->getOption('pagesDir').$path,
            $app->getOption('baseDir').$app->getOption('mount').$app->getOption('pagesDir').$path,
        ];
    }

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
                    $content = new Utils\Replace($content, $app->getPageVars());
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

    private function getPageComponent(string $subExtension = '', string $finalExtension = null):string {
        $baseDir = rtrim($this->page->getFolderPath(), '/');
        $fileName = ltrim($this->page->getName(), '/');
        $extension = is_null($finalExtension) ? $this->page->getExtension() : $finalExtension;
        return $baseDir.'/'.$fileName.$subExtension.$extension;
    }

    use IncludeTrait;

    

    //For User in Template or Pages
    public function setPage(File $page) {
        $this->page = $page;

        //RenderPage onSet
        ob_start();
        $app = $this;

        //AllParts
        if($this->includePagePartPhp('filter')) exit();
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
    public function autoFileRouter():void {
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

    public static function loadFolder(string $projectFolder) {

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
            self::loadFolder($appFolder);
        }
        if(strpos(($uri), $when) !== false) {
            self::loadFolder($appFolder);
        }
    }

    public static function handleMultipleRequestUris(array $requestUris) {
        foreach($requestUris as $requestPattern => $requestUri) {
            self::handleRequestUri($requestPattern, $requestUri);
        }
    }

}