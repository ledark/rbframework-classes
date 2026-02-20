<?php 

namespace Framework;

use Framework\Router;
use Exception;

class App {

    public $collection_dir = '_app/collections/';

    private $route_namespace = 'Route\\';

    private string $appDirectory;
    private array $autoloaders = [];

    private Router $router;

    public function setAppDirectory(string $appDirectory):self{
        $this->appDirectory = $appDirectory;
        return $this;
    }

    /**
     * adicione custom autoloaders que serão inclusos em spl_autoload_register
     * @param callable $autoload
     * @return ExampleApp
     */
    private function addAutoLoader(callable $autoload):self {
        $this->autoloaders[] = $autoload;
        return $this;
    }

    public function useRouter(Router $router) {
        $this->router = $router;
        return $this;
    }

    public function run() {
        //Validate
        if(!isset($this->appDirectory)) {
            throw new Exception("App directory not set.");
        }

        //autoloaders
        $appDirectory = $this->appDirectory;
        $this->addAutoLoader(function($class_name) use ($appDirectory) {
            $class_name = rtrim($appDirectory, '/').'/'.str_replace('\\', '/', $class_name).'.php';
            if(!file_exists($class_name)) {
                throw new Exception("Class not found: ".$class_name);
            }
            include $class_name;
        });

        $router = $this->router;
        $glob_routerdir = glob($appDirectory.'/Route/*.php');
        foreach($glob_routerdir as $route_file) {
            $full_namespace = trim($this->route_namespace, '\\').'\\'.basename($route_file, '.php');
            $router->addNamespace($full_namespace);
        }

    }

}