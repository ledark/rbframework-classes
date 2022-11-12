<?php 

namespace RBFrameworks;

use Bramus\Router\Router;
use RBFrameworks\Core\Utils\Strings as Str;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Response;
use RBFrameworks\Core\Types\Pattern;
use ReflectionFunction;

/**
 * depois de criar uma classe que contenha a Api, basta ir em qualquer lugar onde tenha uma variável do tipo $router = new Bramus\Router\Router();
 * Então chamar em: $router->mount('/minha-base', (new myCustomRoutes\Api($router))->getRoutes()) );
 * Também é possível usar addWork('minhaVar', $mixedValue) para habilitar a recuperação disso em $this->getWorkers()['minhaVar'];
 * 
 *  
 */

class Api {

    protected $router;
    protected $routes = [];
    protected $workers = [];
    protected $servers = [];

    public function __construct(Router $router) {
        $this->router = $router;
        return $this->getRoutes();
    }

    public function getRoutes():callable {
        $api = $this;
        $router = $this->router;
        return function() use ($router, $api) {

            $extendedClass = new \ReflectionClass($api); 
            foreach($extendedClass->getMethods( \ReflectionMethod::IS_PUBLIC ) as  $method) {

                //Ignore Self
                if( $method->class == 'Core\Api' ) continue;

                //UseParent
                $api->addSmartRoute($method, $api);
            }

        };
    }

    private function addSmartRoute(\ReflectionMethod $method, $api) {
        $args = $method->getParameters();
        $partUriName = Str\Dispatcher::camelcased2sef($method->name);
        $partsUriName = explode('-', $partUriName);

        //Method
        $handler = (in_array($partsUriName[0], ['get', 'post', 'put', 'delete', 'options'])) ? array_shift($partsUriName) : 'get';
        $handler = strtoupper($handler);

        //BaseUrl
        $finalUriName = implode('-', $partsUriName);
        foreach($args as $parameter) {
            $finalUriName.= $this->getPatternFromParameter($parameter);
        }

        //ClosureCalls 
        $closureCallEval = function() use ($method, $api) {
            $return = call_user_func_array([$api, $method->name], func_get_args());
            return is_array($return) ? Response::json($return) : $return;
        };

        //Assertions
        if(isset($this->routes[$handler.$finalUriName])) throw new \Exception("Duplicate Route for URI $finalUriName");
        $this->routes[$handler.$finalUriName] = true;

        //addSmartRoute
        $api->router->match($handler, "/".$finalUriName, $closureCallEval);

    }

    private function getPatternFromParameter(\ReflectionParameter $parameter):string {
        $pattern = new Pattern($parameter);
        return $pattern->getPattern();
    }
    
    public function addWorker(string $name, $mixed):object {
        $this->workers[$name] = $mixed;
        return $this;
    }

    public function getWorkers():array {
        return($this->workers);
    }
    
    public function addServer(string $server):object {
        $this->addServers([$server]);
        return $this;
    }

    public function addServers(array $servers):object {
        $this->servers = array_merge($this->servers, $servers);
        return $this;
    }

    public function getServers():array {
        return $this->servers;
    }

}