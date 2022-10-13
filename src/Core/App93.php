<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Session;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;

class App93 {

    public $session;
        
    public function __construct() {
        Plugin::load("smart_replace");
        Plugin::load("logdebug");
        Plugin::load("session");

        $this->session = new Session();

        Plugin::load('ecom3utils');
        Plugin::load('helper');

        $this->registerErrorCallback();
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
        
        define('HTTPSITE', $RBVars['httpSite']);
        
        
        if($_SERVER['REQUEST_SCHEME'] == 'https://') {
            define('IS_HTTPS', true); 
            define('HTTPSITESSL', $RBVars['httpSite']);
            define('HTTPSITENOSSL', str_replace('https://', 'http://', $RBVars['httpHost']) . substr($_SERVER['SCRIPT_NAME'], 0, -9));
        
        } else { 
            define('IS_HTTPS', false); 
            define('HTTPSITENOSSL', $RBVars['httpSite']);
            define('HTTPSITESSL', str_replace('http://', 'https://', $RBVars['httpHost']) . substr($_SERVER['SCRIPT_NAME'], 0, -9));
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

    }
    
}