<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Utils\Arrays;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Debug;

class Config
{
    public static function getCollectionDir():string {
        if(is_dir(__DIR__.'/../../collections')) return __DIR__.'/../../collections/'; //_app/class/Core/ or src/Core/
        if(is_dir('./collections')) return './collections/';
        if(is_dir('_app/collections/')) return '_app/collections/';
        if(!function_exists('get_collection_dir')) {
            throw new \Exception('Create get_collection_dir() function in project that returns the collection dir');
            function get_collection_dir():string { return '/_app/collections/'; }
        }
        return get_collection_dir();        
    }

    public static function print(string $name, string $collections_dir = null) {
        echo self::get($name, $collections_dir);
    }
    
    public static function echo(string $name, string $collections_dir = null) {
        echo self::get($name, $collections_dir);
    }

    public static function assigned(string $name, $default = '', $collections_dir = null) {
        $res = self::get($name, $collections_dir);
        return is_null($res) ? $default : $res;
    }

    public static function get(string $name, string $collections_dir = null) {
        $dados = self::include_file($name, $collections_dir);
        if(is_array($dados)) return $dados;
        return self::get_collection($name, $collections_dir);
    }

    private static function include_file(string $name, string $collections_dir = null) {
        if(is_null($collections_dir)) $collections_dir  = self::getCollectionDir();
        if(file_exists($collections_dir.$name.'.php')) {
            return include($collections_dir.$name.'.php');
        }
        $name = str_replace('.', '/', $name);
        
        if (file_exists($collections_dir.$name.'.php')) {
            return include($collections_dir.$name.'.php');
        }
        
        return null;
    }

    private static function include_collection_file(string $filename, callable $errorCallback = null, string $collections_dir = null) { //array or null
        if(is_null($collections_dir)) $collections_dir  = self::getCollectionDir();
        $filename = str_replace('.', '/', $filename);

        $file = new File($collections_dir.$filename);
        $file->clearSearchFolders();
        if(!is_null($collections_dir)) $file->addSearchFolder($collections_dir);
        $file->addSearchExtension('.php');

        if(!$file->hasFile()) return is_callable($errorCallback) ? $errorCallback() : array();
        return include($file->getFilePath());
    }

    private static function get_collection(string $name, string $collections_dir = null) {

        if(strpos($name, '.') !== false) {
            return self::get_collection_withdot($name, $collections_dir);
        }

        return self::include_collection_file($name, function(){
            return null;
        }, $collections_dir);
    }

    private static function get_collection_withdot(string $name, $collections_dir) {

        if(is_null($collections_dir) or empty($collections_dir)) $collections_dir = self::getCollectionDir();

        $collection_paths = explode('.', $name);
        $collection_name = array_shift($collection_paths);
        $collection_path = implode('.', $collection_paths);
                

        $collection = self::include_collection_file($collection_name, null, $collections_dir);

        

        
        return Arrays::getValueByDotKey($collection_path, $collection, null, '.');
    }

    public static function getCollectionNames():array {
        $collections_dir = self::getCollectionDir();
        $files = glob($collections_dir.'/*.php');
        $names = array();
        foreach($files as $file) {
            $names[] = str_replace('.php', '', basename($file));
        }
        return $names;
    }

}
