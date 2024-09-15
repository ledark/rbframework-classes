<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Api\HandlerResponse;
use RBFrameworks\Core\Response;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Utils\Strings\Dispatcher;
use Bramus\Router\Router;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Types\File;
use ReflectionClass;
use ReflectionException;
use DirectoryIterator;
use RBFrameworks\Core\Utils\ExtendedReflectionClass;

/**
 * Example of Use API
 * use Api\Api;
 * $routerApi = new Api();
 * $routerApi->addNamespace('\Api\Banners');
 * $routerApi->addNamespace('\Api\Produtos\Destaque');
 * $routerApi->addNamespace('\Api\Produtos\Busca');
 * $routerApi->addNamespace('\Api\Newsletter');
 * $routerApi->run();
 *
 * And use the annotations in the class methods, that supports:
 * -- @route GET|POST|PUT|DELETE|OPTIONS /api/uri
 * -- @status 200
 * -- @utf8 true|false or null to dont perform any action
 * -- @before \function\name that not use any args
 * -- @response html|text|json|css|javascript|file|redirect|image
 * -- @descr description of the method, that can be used to perform some actions like log or history, using:
 * ---- @history to push the route to the history
 * ---- @log to log the description in a life log apiLogger
 */
class Api {
    
    public $namespaces = [];
    public $prefix = '';
    public $fn404 = null;
    public $router = null;
    public $mountOn = '';

    public static function autoload(string $directory, string $namespace = "", callable $on404 = null) {
        $namespace = rtrim($namespace, '\\').'\\';
        $router = new self();
        foreach (new DirectoryIterator($directory) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir()) continue;
            $name = basename($fileInfo->getFilename(), '.php');
            $router->addNamespace($namespace.$name);
        }
        $router->fn404 = function() use ($router, $on404) {
	    if(is_callable($on404)) {
               $on404($router);
            }
	};
        $router->run();
    }


    public static function RouteOn(string $namespace) {
        $router = new self();
        $router->addNamespace($namespace);
        $router->run();
    }

    public function __construct() {
        $this->router = new Router();
    }

    public function mountOn(string $mountOn) {
        $this->mountOn = $mountOn;
    }

    public function addRoutePrefix(string $prefix) {
        $this->prefix = $prefix;
    }

    public function addNamespace(string $namespace) {
        $this->namespaces[] = $namespace;
    }

    public function run() {
        $router = $this->router;
        foreach($this->namespaces as $namespace) {
            try {
                $class = new ReflectionClass($namespace);
            } catch (ReflectionException $e) {
                continue;
            }
            foreach($class->getMethods() as $method) {
                $annotation = $method->getDocComment();
                if($annotation === false) continue;

                $routeAnotation = preg_match_all('/@route\s+(GET|POST|PUT|DELETE|OPTIONS)\s+(.*)/um', $annotation, $matches);
                if($routeAnotation === false) continue;
                if(count($matches) < 3) continue;

                

                foreach($matches[1] as $routeKey => $routeMethod) {
                    
                    $routeUri = $matches[2][$routeKey];
                    $routeUri = $this->prefix.trim($routeUri);

                    //if($_SERVER['REQUEST_METHOD'] != $routeMethod) continue;
                    //if(strpos($_SERVER['REQUEST_URI'], $routeUri) === false) continue;

               //     $router->mount($this->mountOn, function() use ($router, $namespace, $method, $routeMethod, $routeUri) {



                    $router->match($routeMethod, $routeUri, function() use ($namespace, $method, $routeUri) {
                        $forceEncodeUTF8 = null; 
                        $responseCode = 200;
                        $responseType = 'text';
                        $annotation = $method->getDocComment();
                        if($annotation !== false) {

                            //getUTF8
                            if(preg_match('/@utf8\s+(true|false)/', $annotation, $matches)) {
                                if($matches[1] === 'true') {
                                    $forceEncodeUTF8 = true;
                                } else {
                                    $forceEncodeUTF8 = false;
                                }
                            }

                            //getStatusCode
                            if(preg_match('/@status\s+(\d+)/', $annotation, $matches)) {
                                $responseCode = intval($matches[1]);
                                if($responseCode < 100 || $responseCode > 599) {
                                    $responseCode = 200;
                                }
                            }

                            //beforeMiddleware
                            if(preg_match('/@before\s+(.+)/', $annotation, $matches)) {                                
                                $beforeMiddleware = $matches[1];
                                $beforeMiddleware = trim($beforeMiddleware);
                                call_user_func($beforeMiddleware);
                            }

                            //response
                            if(preg_match('/@response\s+(html|text|json|css|javascript|file|redirect|image)/', $annotation, $matches)) {
                                $responseType = $matches[1];
                            }

                            //description
                            if(preg_match('/@descr\s+(.+)/', $annotation, $matches)) {
                                $description = $matches[1];

                                //push to the history
                                if(strpos($description, '@history') !== false) {
                                    $_SESSION['history'] = isset($_SESSION['history']) ? $_SESSION['history'] : [];
                                    $_SESSION['history'] = array_slice($_SESSION['history'], -10);
                                    $_SESSION['history'] = array_merge([$routeUri], $_SESSION['history']);
                                }

                                //push to the log
                                if(strpos($description, '@log') !== false) {
                                    Debug::log(str_replace('@log', '', $description), [], $routeUri, 'ApiLogger');
                                }

                            }
                            
                        }
                        $class = new $namespace();
                        $method = $method->getName();
                        $result = $class->$method();

                        $charset = function() use($forceEncodeUTF8):string {
                            if(is_null($forceEncodeUTF8)) {
                                return ($forceEncodeUTF8 == true) ? '; charset=utf-8' : '; charset=iso-8859-1';
                            } else {
                                return '';
                            }
                        };

                        $ApiHandlerResponse = new HandlerResponse();
                        $ApiHandlerResponse
                            ->setCharset($charset()) //canbe utf-8 | iso-8859-1 | empty
                            ->setType($responseType) //canbe html|text|json|css|javascript|file|redirect|image
                            ->setResult($result) //canbe mixed
                            ->setResponseCode($responseCode); //canbe int

                        if(isset($forceEncodeUTF8) and is_bool($forceEncodeUTF8)) {
                            $ApiHandlerResponse->utf8 = $forceEncodeUTF8;
                        }
                        
                        $ApiHandlerResponse
                            ->send();

                            /*
                        if(is_array($result) or $responseType == 'json') {
                            if(is_null($forceEncodeUTF8)) $forceEncodeUTF8 = false;
                            Response::json($result, $forceEncodeUTF8, $responseCode);
                        } else if($responseType == 'html') {
                            if(!headers_sent()) {
                                header('Content-Type: text/html'.$charset());
                            }
                            echo $result;
                        } else if($responseType == 'css') {
                            if(!headers_sent()) {
                                header('Content-Type: text/css'.$charset());
                            }
                            echo $result;
                        } else if($responseType == 'javascript') {
                            if(!headers_sent()) {
                                header('Content-Type: text/javascript'.$charset());
                            }
                            echo $result;                                                        
                        } else if($responseType == 'file') {
                            if(!headers_sent()) {
                                header('Content-Type: application/octet-stream');
                            }
                            readfile($result);
                        } else if($responseType == 'image') {
                            $image = new File($result);
                            File::readFile($image->getFilePath());                            
                        } else if($responseType == 'redirect') {
                            if(!headers_sent()) {
                                header('Location: '.$result);
                            }
                        } else {
                            if(!headers_sent()) {
                                header('Content-Type: text/plain'.$charset());
                            }
                            echo $result;                            
                        }
*/
                        exit();
                    });

                  //  });                   //end mount
                }

            }
        }
        $fn404 = ($this->fn404 !== null) ? $this->fn404 : function() use ( $router) { echo "NOT FOUND". $router->getCurrentUri();  };
        $router->set404($fn404);
        $router->run();
    }

    public function loadFromDatabase(string $traitNamespace, string $parentNamespace, string $parentFilePath, string $cache_id = null) {
        if(is_null($cache_id)) {
            $cache_id = md5($traitNamespace);
        }

        Cache::stored(function() use($traitNamespace, $parentFilePath) {

        $getUseClasses = function($classPath) use ($traitNamespace):string {
            $usedClasses = "";
            $classLines = file($classPath);
            foreach($classLines as $ln => $line) {
                if(strpos($line, 'use ') !== false) {
                    if(strpos($line, $traitNamespace) !== false) {
                        continue;
                    }
                    $usedClasses.= $line."\r\n";
                }
            }
            return $usedClasses;
        };

        //Definindo Diretório
        $parsed_file_location = Config::assigned('location.cache.default', 'log/cache/');
        $parsed_file_location = rtrim($parsed_file_location, '/').'/autoload_class/';
        Directory::mkdir($parsed_file_location);

        $traitNamespace = ltrim($traitNamespace, '\\');
        $directory = explode('\\', $traitNamespace);
        array_pop($directory);
        foreach($directory as $dir) {
            $parsed_file_location = $parsed_file_location.$dir.'/';
            Directory::mkdir($parsed_file_location);
        }
        $parsed_file_location = $parsed_file_location.$traitNamespace.'.php';

        if(file_exists($parsed_file_location)) {
            unlink($parsed_file_location);
        }


        if(!file_exists($parsed_file_location)) {
            $result = '<?php

            '.$getUseClasses($parentFilePath).'

            trait '.$traitNamespace.' {
                ';
            $fromDatabase = Database::getInstance()->query("SELECT * FROM ?_rbf_api WHERE `status` > 0");
            foreach($fromDatabase as $row) {
                $methodName = Dispatcher::camelcased($row['title']);
                if($row['utf8'] == 1) {
                     $row['utf8'] = '@utf8 true';
                } else
                if($row['utf8'] == 0) {
                    $row['utf8'] = '@utf8 false';
                } else {
                    $row['utf8'] = "";
                }
                ob_start();
                echo '
                /**
                 * @route '.$row['method'].' '.$row['route'].'
                 * @status '.$row['status'].'
                 * @response '.$row['response'].'
                 * '.$row['utf8'].'
                 * '.$row['before'].'
                 */
                public function '.$methodName.'() {
                    ';
                    echo $row['phpcode'];
                    echo '
                }
                ';
                $result.= ob_get_clean();
                unset($row);
            }
            $result.= '}';

            file_put_contents($parsed_file_location, $result);
        }
    }, 'api-database-'.$cache_id, 60*60*24*30);
        //include_once $parsed_file_location;
        $this->addNamespace($parentNamespace);
    }

    /**
     * @route POST /api/banners/sample-json
     * @route GET /api/banners/sample-json
     * @status 200
     * @utf8 true
     */    
    public function sampleJson() {
        return [
            'sample-line1' => "this arrays works on GET or POST",
            'sample-line2' => "the status code is 200 and the response is encoded in UTF8",
            'sample-line3' => "the content type is json",
        ];
    }

    /**
     * @route GET|POST /api/example/xyzstr
     */
    public function sampleString() {
        return 'this works on GET to /api/example/xyzstr';
    }

    public function sampleNoRouted() {
        return 'this is never routed because is not annotated';
    }

    /**
     * @route GET /api/example/xyz anotherparam
     * @response html
     */    
    public function sampleWrongRouted() {
        return 'this is never called because the route is wrong';
    }    

}