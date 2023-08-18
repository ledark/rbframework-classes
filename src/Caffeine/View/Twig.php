<?php

/**
 * View\Twig
 *
 * Core\View Twig Bridge.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */

namespace RBFrameworks\Caffeine\View;

class Twig implements Adapter {
    protected static $loader = null;
    protected static $templatePath = '';
    protected static $twig = null;
    const EXTENSION = '.php';

    public function __construct($path=null,$options=[]){
      static::$templatePath = rtrim($path,'/').'/';
      static::$loader = new \Twig_Loader_Filesystem($path);
      static::$twig   = new \Twig_Environment(static::$loader,$options);
    }

    public function __call($n,$p){
      return call_user_func_array([static::$twig,$n],$p);
    }

    public static function __callStatic($n,$p){
      return forward_static_call_array([static::$twig,$n],$p);
    }

    public function render($template,$data=[]){
    	try {
        return static::$twig->render($template.static::EXTENSION,$data);
      } catch(\Exception $e) {
      	return "<!-- ERROR --><pre class=\"error\"><code>$e</code></pre><!-- /ERROR -->";
      }
    }

    public static function exists($path){
        return is_file(static::$templatePath.$path.static::EXTENSION);
    }

    public static function addGlobal($key,$val){
      static::$twig->addGlobal($key,$val);
    }

    public static function addGlobals(array $defs){
      foreach ((array)$defs as $key=>$val) {
        static::$twig->addGlobal($key,$val);
      }
    }

    public static function addFilter($name,callable $filter){
      static::$twig->addFilter(new \Twig_SimpleFilter($name, $filter));
    }

    public static function addFunction($name,callable $function){
      static::$twig->addFunction(new \Twig_SimpleFunction($name, $function));
    }

    public static function addFilters(array $defs){
      foreach ((array)$defs as $key=>$val){
        if (is_callable($val)){
          static::$twig->addFilter(new \Twig_SimpleFilter($key, $val));
        }
      }
    }

}
