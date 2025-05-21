<?php
namespace Framework;

use Framework\Router\HandlerResponse;
use Bramus\Router\Router as BramusRouter;
use ReflectionClass;
use ReflectionException;
use DirectoryIterator;
use Framework\Cache;


/**
 * Example of Use API
 * use Framework\Router;
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
class Router {
    public $namespaces = [];
    public $mounts = [];
    public $prefix = '';
    public $fn404 = null;
    public $router = null;

    public function __construct() {
        $this->router = new BramusRouter();
    }

    public function addNamespace(string $namespace, string|array $mount = '') {
        $generateIndex = count($this->namespaces);
        $this->namespaces[$generateIndex] = $namespace;
        $this->mounts[$generateIndex] = is_string($mount) ? [$mount] : $mount;
    }

    private static function getStatusCode(string $annotation):int {
        if(preg_match('/@status\s+(\d+)/', $annotation, $matches)) {
            $responseCode = intval($matches[1]);
            if($responseCode < 100 || $responseCode > 599) {
                $responseCode = 200;
            }
        }
        return $responseCode??200;
    }

    public static function getResponse(string $annotation):string {
        if(preg_match('/@response\s+(html|text|json|css|javascript|file|redirect|image|htmx)/', $annotation, $matches)) {
            $responseType = $matches[1];
        }
        return $responseType??'text';
    }

    public static function getUtf8(string $annotation):bool|null {
        if(preg_match('/@utf8\s+(true|false)/', $annotation, $matches)) {
            if($matches[1] === 'true') {
                $forceEncodeUTF8 = true;
            } else {
                $forceEncodeUTF8 = false;
            }
        }
        return $forceEncodeUTF8??null;
    }

    public static function getBefore(string $annotation):string {
        if(preg_match('/@before\s+(.+)/', $annotation, $matches)) {
            $beforeMiddleware = $matches[1];
            $beforeMiddleware = trim($beforeMiddleware);
        }
        return $beforeMiddleware??'';
    }
    public static function getDescr(string $annotation):string {
        if(preg_match('/@descr\s+(.+)/', $annotation, $matches)) {
            $description = $matches[1];
        }
        return $description??'';
    }
    public static function getCache(string $annotation):string {
        if(preg_match('/@cache\s+(.+)/', $annotation, $matches)) {
            $cache_config = $matches[1];
        }
        return $cache_config??'';
    }

    public function getRoutes():array {
        $routes = [];
        foreach($this->namespaces as $namespace_index => $namespace) {
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
                    foreach($this->mounts[$namespace_index] as $mount) {

                        $routeUri = $matches[2][$routeKey];
                        $routeUri = $this->prefix.trim($routeUri);
                        $routeUri = trim($mount, '/').'/'.ltrim($routeUri, '/');

                        $resolveUtf8 = function($annotation) {
                            $value = self::getUtf8($annotation);
                            return is_null($value) ? 'null' : ($value ? 'true' : 'false');
                        };

                        $routes[] = [
                            'method' => $routeMethod,
                            'uri' => $routeUri,
                            'namespace' => $namespace,
                            'controller' => $method->getName(),
                            'file' => $class->getFileName().':'.$method->getStartLine(),
                            'status' => self::getStatusCode($annotation),
                            'response' => self::getResponse($annotation),
                            'utf8' => $resolveUtf8($annotation),

                            'before' => self::getBefore($annotation),
                            'descr' => self::getDescr($annotation),
                            'cache' => self::getCache($annotation),
                        ];
                    }
                }
            }
        }
        return $routes;
    }



    public function run() {
        $router = $this->router;
        foreach($this->namespaces as $namespace_index => $namespace) {
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
                    foreach($this->mounts[$namespace_index] as $mount) {

                        $routeUri = $matches[2][$routeKey];
                        $routeUri = $this->prefix.trim($routeUri);
                        $routeUri = trim($mount, '/').'/'.ltrim($routeUri, '/');


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
                                $responseCode = self::getStatusCode($annotation);
                                /*
                                if(preg_match('/@status\s+(\d+)/', $annotation, $matches)) {
                                    $responseCode = intval($matches[1]);
                                    if($responseCode < 100 || $responseCode > 599) {
                                        $responseCode = 200;
                                    }
                                }
                                */

                                //beforeMiddleware
                                if(preg_match('/@before\s+(.+)/', $annotation, $matches)) {
                                    $beforeMiddleware = $matches[1];
                                    $beforeMiddleware = trim($beforeMiddleware);
                                    call_user_func($beforeMiddleware);
                                }

                                //response
                                $responseType = self::getResponse($annotation);
                                /*
                                if(preg_match('/@response\s+(html|text|json|css|javascript|file|redirect|image|htmx)/', $annotation, $matches)) {
                                    $responseType = $matches[1];
                                }
                                */

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

                                //cache
                                if(preg_match('/@cache\s+(.+)/', $annotation, $matches)) {
                                    $cache_config = $matches[1];
                                }

                            }

                            $charset = function() use($forceEncodeUTF8):string {
                                if(is_null($forceEncodeUTF8)) {
                                    return ($forceEncodeUTF8 == true) ? '; charset=utf-8' : '; charset=iso-8859-1';
                                } else {
                                    return '';
                                }
                            };

                            if(isset($cache_config)) {
                                $result = $this->getCachedResult($cache_config, function() use ($namespace, $method) {
                                    $class = new $namespace();
                                    $method = $method->getName();
                                    return $class->$method();
                                });
                                if($result !== null) {
                                    $ApiHandlerResponse = new HandlerResponse();
                                    $ApiHandlerResponse
                                        ->setCharset($charset()) //canbe utf-8 | iso-8859-1 | empty
                                        ->setType($responseType) //canbe html|text|json|css|javascript|file|redirect|image
                                        ->setResult($result) //canbe mixed
                                        ->setResponseCode($responseCode); //canbe int
                                    $ApiHandlerResponse->send();
                                    exit();
                                }
                            }

                            $class = new $namespace();
                            $method = $method->getName();
                            $result = $class->$method();

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

                                exit();

                    });
                }
            }
        }
    }
    $fn404 = ($this->fn404 !== null) ? $this->fn404 : function() use ( $router) { echo "NOT FOUND". $router->getCurrentUri();  };
    $router->set404($fn404);
    $router->run();
    }

    private function getCachedResult(string $cache_config, callable $result) {
        $cacheConfig = [
            'type' => 'all',
            'ttl' => 60*60*24*7,
        ];
        if(strpos($cache_config, '|') !== false) {
            $cache_config = explode('|', $cache_config);
            foreach($cache_config as $cc) {
                if($cc == 'user') {
                    $cacheConfig['type'] = 'user';
                }
                if(strpos($cc, '*') !== false) {
                    $cacheConfig['ttl'] = eval('return '.$cc.';');
                }
                if(is_numeric($cc)) {
                    $cacheConfig['ttl'] = $cc;
                }
            }
        } else {
            if(strpos($cache_config, 'user') !== false) {
                $cacheConfig['type'] = 'user';
            } else {
                $cacheConfig['type'] = 'all';
            }
        }
        //@todo cache usar o forminput getfromanywhere para pegar o cache
        $cacheid = [
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['QUERY_STRING'],
        ];
        if($cacheConfig['type'] == 'user') {
            $cacheid[] = session_id();
        }
        $cacheid = md5(implode('|', $cacheid));
        return Cache::stored($result, $cacheid, $cacheConfig['ttl']);
    }

}