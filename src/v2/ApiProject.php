<?php 

namespace Utils;

use DirectoryIterator;
use RBFrameworks\Core\Api;

class ApiProject {

    /**
     * @param string $directory - Diretório onde o sistema poderá encontrar outras pastas, e essa pastas deverão conter um arquivo config.php
     * @param Api $router - Instância do Router já instanciada que colocará as rotas como explicitadas no arquivo config.php
     * @example new ApiProject('src/Projects/', $router);
     * A estrutura do arquivo config deve ser:
     *
     * return [

     *  'namespace' => '\ExampleNewWhateverProject',
     *  'mount_prefix' => '/example-new-whatever-project/',

     *  'recompile' => true,

     *  'folders' => [
     *      'cache' => 'cache',
     *      'routes' => 'routes',
     *      'classes' => 'class',
     *  ],

     * ]
     */

    private array $projects = [];

    public function __construct(string $directory, Api &$router) {

        if(!is_dir($directory)) throw new \Exception(sprintf('Directory %s not found', $directory));

        foreach(new DirectoryIterator($directory) as $file){
            if($file->isDot()) continue;
            if($file->isFile()) continue;
            $PROJECT_FOLDER = $directory.$file->getBasename().'/';
            if(file_exists($PROJECT_FOLDER.'/config.php')) {

                $config = include($PROJECT_FOLDER.'/config.php');
                $this->projects[$PROJECT_FOLDER] = $config;
        
                if(!isset($config['folders'])) continue;
                if(!isset($config['recompile']) or $config['recompile'] == false) continue;
        
                //Register Autoload
                spl_autoload_register(function($CLASS_NAME) use ($PROJECT_FOLDER, $config) {
                    if (!class_exists($CLASS_NAME)) {
                        $search = [
                            $PROJECT_FOLDER.$CLASS_NAME.'.php',
                            $PROJECT_FOLDER.str_replace($config['namespace'], '', $CLASS_NAME).'.php',
                            $PROJECT_FOLDER.$config['folders']['routes'].str_replace(ltrim($config['namespace'], '\\/'), '', str_replace('\\', '/', $CLASS_NAME)).'.php',
                            $PROJECT_FOLDER.$config['folders']['classes'].str_replace(ltrim($config['namespace'], '\\/'), '', str_replace('\\', '/', $CLASS_NAME)).'.php',
                        ];
        
                        foreach($search as $file){
                            if(file_exists($file)) {
                                include($file);
                                return;
                            }
                        }
                    }
                });
        
                //ReadRoutes
                /*
                foreach(new DirectoryIterator($PROJECT_FOLDER.'/'.ltrim($config['folders']['routes'], '/')) as $file){
                    if($file->isDot()) continue;
                    if($file->isDir()) continue;
                    $router->addNamespace($config['namespace'].'\\'.$file->getBasename('.php'));
                }
                */

                $this->readRoutes($PROJECT_FOLDER.'/'.ltrim($config['folders']['routes'], '/'), $config, $router);
        
            }
        }        
        
    }

    private function readRoutes(string $directory, array $config, Api &$router) {
        foreach(new DirectoryIterator($directory) as $file){
            if($file->isDot()) continue;
            if($file->isDir()) continue;
            $router->addNamespace($config['namespace'].'\\'.$file->getBasename('.php'));
        }
    }

}