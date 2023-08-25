<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Response;
use RBFrameworks\Core\Config;
use Bramus\Router\Router;
use RBFrameworks\Core\Types\File;
use ReflectionClass;
use ReflectionException;

/**
 * Example of Use API
 * use Api\Api;
 * $routerApi = new Api();
 * $routerApi->addNamespace('\Api\Banners');
 * $routerApi->addNamespace('\Api\Produtos\Destaque');
 * $routerApi->addNamespace('\Api\Produtos\Busca');
 * $routerApi->addNamespace('\Api\Newsletter');
 * $routerApi->run();
 */
class Api {
    
    public $namespaces = [];
    public $prefix = '';
    public $fn404 = null;
    public $router = null;
    public $mountOn = '';

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



                    $router->match($routeMethod, $routeUri, function() use ($namespace, $method) {
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
                                $statusCode = intval($matches[1]);
                                if($statusCode < 100 || $statusCode > 599) {
                                    $statusCode = 200;
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