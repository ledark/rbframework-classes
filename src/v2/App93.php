<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Session;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;
use Bramus\Router\Router;

class App93 {

    public $session;
    public $router;
    public $router_justinc = [];
    public $router_customfiles = [];
    public $router_customdirectories = [];
        
    public function __construct() {
        Plugin::load("smart_replace");
        Plugin::load("logdebug");
        Plugin::load("session");

        $this->session = new Session();

        Plugin::load('ecom3utils');
        Plugin::load('helper');

        $this->registerErrorCallback();

        $this->router = new Router();
    }

    private function registerErrorCallback():void {
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                // This error code is not included in error_reporting
                return;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
    }

    public function extractRBVars():void {
        global $RBVars;
        $RBVars = [
            'cliente' => [
                'nomeFantasia'      => Config::assigned('cliente.nomeFantasia', 'RBFrameworks'),
                'cod_cliente'		=> Config::assigned('cliente.cod_cliente', 0),
                'cod_chamado'		=> Config::assigned('cliente.cod_chamado', 0),
                'appVersion'        => Config::assigned('cliente.appVersion', '88.2'),
                'prevClient'        => Config::assigned('cliente.prevClient'),
            ],
            'framework' => [
                'appVersion'        => Config::assigned('cliente.appVersion', '93.1.0'),
                'prevClient'        => Config::assigned('cliente.prevClient'),                
            ],
            'configs' => [
                'url_clean' 	    => Config::assigned('rbf93.configs.url_clean',          true   ),   
                'isProducao'  	    => Config::assigned('rbf93.configs.isProducao',         false  ),   
                'autocreate'  	    => Config::assigned('rbf93.configs.autocreate',         true   ),      //Auto Criar arquivos de Controller, Models, Views, etc
                'debug_mode'  	    => Config::assigned('rbf93.configs.debug_mode',         true   ),      //Exibir conteúdo adicional de debug?
                'CacheScripts'      => Config::assigned('rbf93.configs.CacheScripts',       false  ),   
                'RBVarsGenerate'    => Config::assigned('rbf93.configs.RBVarsGenerate',     true   ),           //Gerar sempre as RBVars. Voc� pode desativar isso se o arquivo RAIZ/_app/vars.inc j� tiver sido criado e configurado.
                'scriptCaches'      => Config::assigned('rbf93.configs.scriptCaches',       true   ),      //Tenta colocar scripts em Caches
                'classCacheClear'   => Config::assigned('rbf93.configs.classCacheClear',    true   ),           //For�ar a limpeza do Cache
                'ClassDEBUG'        => Config::assigned('rbf93.configs.ClassDEBUG',         true   ),   
                'pagDefault'        => Config::assigned('rbf93.configs.pagDefault',         'home' ),      //P�gina padr�o para o sistema procurar quando nenhuma vari�vel $_GET['pag'] for definida
                'dirRB'             => Config::assigned('rbf93.configs.dirRB',              'htm/' ),       //Pasta padr�o para o sistema procurar pelo arquivo $_GET['pag']
                'forceMobile'		=> Config::assigned('rbf93.configs.forceMobile',        false  ),   	//Pasta padr�o para o sistema procurar pelo arquivo $_GET['pag']    
                'forceHTTPs'        => Config::assigned('rbf93.configs.forceHTTPs',         true   ),                 
            ],
            'server' => [
                'fileController'	=>	Config::assigned('rbf93.server.fileController', "website/controller/[INPUT_FILE].php"),
                'fileView'			=>	Config::assigned('rbf93.server.fileView', "website/[INPUT_FILE].php"),
                'fileModel'			=>	Config::assigned('rbf93.server.fileModel', "website/controller/[INPUT_FILE].config.php"),
                'viewTemplate'		=>	Config::assigned('rbf93.server.viewTemplate', "website/index.html"),
                'page404'           =>	Config::assigned('rbf93.server.page404', "website/404.html"),
                'base_url'          =>  Config::assigned('rbf93.server.base_url', 'http://localhost'),
            ],
            'database' => [
                'PDOConstruct'      => Config::assigned('database.PDOConstruct', true),		//Desativa o Construtor do PDO para ganho de performace (ele não irá recontruir tabelas e models)
                'PDOErrors'	        => Config::assigned('database.PDOErrors', true),		//Mostrar Erros do PDO
                'ADDErrors'	        => Config::assigned('database.ADDErrors', true),		//Gerar um log em log/logs/doDBv4.ADDERRORS com os campos que não foram adicionados por não existirem na Model		
                'server'            => Config::get('database.server'),
                'login'             => Config::get('database.login'),
                'senha'             => Config::get('database.senha'),
                'database'          => Config::get('database.database'),
                'prefixo'           => Config::get('database.prefixo'),
            ],
            'ecom_exsam' => [
                'id_grupo3'         => Config::assigned('exsam.id_grupo3', 0),
            ],
            'ecom' => [
                'integracoes' => [
                    'rdsation' => [
                        'active' => false,
                    ]
                ],
                'mailto' => [
                    'sender_name'               => '',
                    'sender_mail'               => '',
                    'sender_subject_prefix'     => '',
                    'url_curlprocess_admin'     => '',
                    'url_curlprocess_cliente'   => '',
                    'url_curlprocess_api'       => '',
                ],
            ]
        ];

        $_SERVER['REQUEST_SCHEME'] = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] .'://' : 'http://';        

        $RBVars['httpHost'] = $_SERVER['REQUEST_SCHEME'] . $_SERVER['HTTP_HOST'];
        $RBVars['httpSite'] = $RBVars['httpHost'] . substr($_SERVER['SCRIPT_NAME'], 0, -9); //remove index.php
        $RBVars['httpLink'] = ($RBVars['configs']['url_clean']) ? $RBVars['httpSite'] : $RBVars['httpHost'] . $_SERVER['SCRIPT_NAME'] . '?pag=';
        $RBVars['httpFile'] = $RBVars['httpSite'].'default/';
        
        if(!defined('HTTPSITE')) define('HTTPSITE', $RBVars['httpSite']);
        
        
        if($_SERVER['REQUEST_SCHEME'] == 'https://') {
            if(!defined('IS_HTTPS')) define('IS_HTTPS', true); 
            if(!defined('HTTPSITESSL')) define('HTTPSITESSL', $RBVars['httpSite']);
            if(!defined('HTTPSITENOSSL')) define('HTTPSITENOSSL', str_replace('https://', 'http://', $RBVars['httpHost']) . substr($_SERVER['SCRIPT_NAME'], 0, -9));
        
        } else { 
            if(!defined('IS_HTTPS')) define('IS_HTTPS', false); 
            if(!defined('HTTPSITENOSSL')) define('HTTPSITENOSSL', $RBVars['httpSite']);
            if(!defined('HTTPSITESSL')) define('HTTPSITESSL', str_replace('http://', 'https://', $RBVars['httpHost']) . substr($_SERVER['SCRIPT_NAME'], 0, -9));
        }

        //Unsets
        unset($RBVars['configs']['url_clean']);
        unset($RBVars['framework']);        

    }

    //ForceHTTPS Condicionalmente
    public function forceHTTPs(bool $force = true):void {        
        if(!$force) return;
        global $RBVars;
        if(!isset($RBVars['configs']['forceHTTPs'])) $RBVars['configs']['forceHTTPs'] = false;
        if(strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            if($_SERVER['REQUEST_SCHEME'] == 'https://') {
                $redir = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header("Location: $redir");
            }
            $_SERVER['REQUEST_SCHEME'] = 'http://';
        
        } elseif(strpos($_SERVER['REQUEST_URI'], '/autenticar/admin') !== false) {
            
        
        } else {
            if($_SERVER['REQUEST_SCHEME'] == 'http://' and $RBVars['configs']['forceHTTPs']) {
                $redir = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header("Location: $redir");
            }    
            $_SERVER['REQUEST_SCHEME'] = 'https://';
        }
    }

    public function startRouter():void {
        //InitBasic
        $this->startRouter_onCore();

        //CustomFiles
        $this->startRouter_onFiles();

        if('/'.INPUT_URL != $this->router->getCurrentUri() and $this->router->getCurrentUri() == '/') {
            //logdebug("Bramus\Router não necessário devido a URL ser a raiz");
            unset($router);
            $this->skipRouter();
            
        }

        if(class_exists('\RBRequest\RBRequest')) {
            (new \RBRequest\RBRequest())
            ->parseInputUrl()
            ->run();            
        } else {
            Debug::log('\RBRequest\RBRequest não encontrado');
        }

        $this->startRouter_onMVC();        
        $this->startRouter_onDirectories();

    }

    private function skipRouter():void {
        foreach($this->router_justinc as $file) {
            include($file); 
            //logdebug("\$router_justinc fez o include de $file");
        }
    }

    private function startRouter_onCore():void {
        //Requisições Especiais na URL v93.1.0
        if(strtoupper(substr(INPUT_URL, 0, 5)) == 'CORE/' ) {
            logdebug("Requisição por CORE/");
            $paths = explode('/', INPUT_URL);
            array_shift($paths);
            $keyword = array_shift($paths);
            $args = implode('/', $paths);
            include(RAIZ.'_app/core/'.$keyword.'.php');
            exit();
        }
    }

    private function startRouter_onMVC():void {
//Procurar por Classe com base na URL
$requestClass = INPUT_URL;
$requestClass = explode('/', $requestClass);
$requestMethod = array_pop($requestClass);
$requestClass = implode('/', $requestClass);

if(!empty($requestClass)) { 
    $requestClass = ucwords($requestClass, '/');
    
    //Casos Especiais
    $requestClass = str_replace('Rdstation', 'RDStation', $requestClass);
    
    
    if(file_exists("_app/class/{$requestClass}.php") or class_exists('\\'.str_replace('/', '\\', $requestClass))) {


    //logdebug("Requisição direta para a classe: $requestClass");
    $requestClass = '\\'.str_replace('/', '\\', $requestClass);
    $reflectClass = new \ReflectionClass($requestClass);
        if($reflectClass->hasMethod($requestMethod) and $reflectClass->hasConstant('public') ) {
        Plugin::load("parse");
        $code = <<<CODE
        \$requestClass = new $requestClass();
        \$requestClass->$requestMethod();
        
CODE;
        parse_as_php($code);
            exit();
        }
    }
}
    }

    private function startRouter_onInputUrl():void {

        if(file_exists("_app/routers/".INPUT_URL.'.php')) {
	
            $method = 'GET';
            
            if(file_exists("_app/routers/".INPUT_URL.'.isPost')) $method = 'POST'; else
            if(file_exists("_app/routers/".INPUT_URL.'.isAll')) $method = 'GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD';
            
            
            
            $this->router->match($method, '/'.INPUT_URL.'?.*', function() {
                
                $paths = strpos(INPUT_URL, '/') !== false ? explode('/', INPUT_URL) : array();
                $path_root = array_shift($paths);
                
                $includes_pre = array(
                    '_app/routers/'.$path_root.'/_config.php',
                    '_app/routers/'.$path_root.'/_ini.php',
                );
                $includes_sux = array(
                    '_app/routers/'.$path_root.'/_end.php',
                );
                
                foreach($includes_pre as $include) {
                    if(file_exists($include)) include($include);
                }
                
                
                include("_app/routers/".INPUT_URL.'.php');
                
                foreach($includes_sux as $include) {
                    if(file_exists($include)) include($include);
                }
            });
        }
        
        if(file_exists("_app/routers/".INPUT_URL.'.js')) {
            
            $this->router->get('/'.INPUT_URL, function() {
                header("Content-Type: text/javascript");
                include("_app/routers/".INPUT_URL.'.js');
            });
        }        
    }

    private function startRouter_onFiles():void {
        foreach($this->router_customfiles as $file) {
            if(file_exists($file)) {
                require_once $file;
            }
        }
    }

    private function startRouter_onDirectories():void {
        foreach($this->router_customdirectories as $directory) {
            foreach (new \DirectoryIterator($directory) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                if($fileInfo->isDir()) continue;
                
                //Definição de Variáveis
                $fileInfo_pathname = $fileInfo->getPathname();
                $fileInfo_routername = '/'.basename($fileInfo_pathname, '.php');
                $fileInfo_routername = str_replace('!', '', $fileInfo_routername);
            
                //Router do Arquivo no Diretório
                if(strpos($fileInfo_pathname, '/!') !== false) {
                    $this->router_justinc[] = $fileInfo_pathname;
                } else {
                    $this->router->get($fileInfo_routername, function() use ($fileInfo_pathname) {
                    include($fileInfo_pathname);
                });
                }

                unset($fileInfo_pathname, $fileInfo_routername);
            
            }
        }
    }
    
    public function startRouter_capture_Postback(string $route, $callback = null):void {
        $this->router->all($route, function() use($callback, $route) {
            $content = file_get_contents("php://input");
            $content.= "\r\nREQUEST_URI: ".$_SERVER['REQUEST_URI']."\r\n";
            if(isset($_POST)) {
                ob_start();
                print_r($_POST);
                $content.= ob_get_clean();
            }
            $content.= "\r\nHEADERS: ";
            ob_start();
            print_r(apache_request_headers());
            $content.= ob_get_clean();
            file_put_contents('log/rdstation/'.time().'__'.uniqid(), $content);
            Debug::log($route, ['content' => $content], 'capure_postback', 'rbf93route');
            if(!is_null($callback)) {
                $callback();
            }
            Plugin::load("response");
            response_json([
                'status' => 200,
                'message' => 'URL capturando dados com sucesso'
            ]);
            exit();
        });
    }
    
    public function addCustomRouteFile(string $file):void {
        if(!file_exists($file)) {
            Debug::log("Arquivo de Rotas Customizadas não encontrado: $file");
            return;
        }
        $this->router_customfiles[] = $file;
    }

    public function addCustomRouteDirectory(string $directory):void {
        if(!is_dir($directory)) {
            Debug::log("Diretório de Rotas Customizadas não encontrado: $directory");
            return;
        }
        $this->router_customdirectories[] = $directory;

    }
    
}