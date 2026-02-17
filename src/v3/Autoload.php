<?php

namespace RBFrameworks;

class Autoload {

    public static function start():void {
        spl_autoload_register(['RBFrameworks\Autoload', 'loadClass']);
        set_include_path(get_include_path() . PATH_SEPARATOR . get_root_path());        
    }

    public static function forceHttps():void {
        if( collection('server.force_https') ) {
            if(!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
                header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
    
    public static function loadFunction(string $functionName):void {
        foreach(collection('location.functions') as $functionFile) {
            $functionFile = str_replace('[FUNCTION_NAME]', $functionName, $functionFile);
            if(function_exists($functionName)) {
                return;
            } else
            if (file_exists($functionFile)) {
                require_once $functionFile;
            }
        }
    }

    public static function loadFunctions(array $functionNames):void {
        foreach($functionNames as $functionName) {
            self::loadFunction($functionName);
        }
    }

    public static function loadClass(string $className) {
		if (!class_exists($className)) {            

            $className = (strpos($className, '\\') !== false) ? str_replace('\\', '/', $className) : $className;
            
            foreach (collection('location.class_autoload') as $inc) {

                $inc = str_replace('[CLASS_NAME]', $className, $inc);
                if (file_exists(get_root_path($inc))) {
                    include($inc);
                    return;
                }
            }
		}
    }
}