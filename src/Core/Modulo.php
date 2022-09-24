<?php

namespace Core;

use RBFrameworks\Core\Input;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Session;

/**
 * Arquivos de Módulo são simples arquivos .php colocados em DIRECTORY_MODULES
 * Esses arquivos são criados automaticamente sempre que essa classe for invocada;
 */
class Modulo
{

    public const DIRECTORY_MODULES = './_app/class/Core/Modulos/';

    private static $instance = null;

    private function __construct()
    {
		if(!isset($_SESSION['RBModulos'])) {
			$_SESSION['RBModulos'] = [];
		}
    }

    public static function getInstance()
    {
      if (self::$instance == null)
      {
        self::$instance = new Modulo();
      }
   
      return self::$instance;
    }

    private static function exists($moduleName):bool {

        //Input and Session has Priority
        if(self::hasInInput($moduleName) or self::hasInSession($moduleName)) return true;

        //Fallback by Directory
        if(self::hasInDisabled($moduleName)) return false;
        if(self::hasInDirectory($moduleName)) return true;
        return false;
    }

    /**
     * Modulo::has('feature-name'); // verifica se o módulo feature-name existe.
     * Modulo::has('feature-name', function($pathOfModule...){}); // verifica se o módulo feature-name existe e executa os callbacks de acordo
     * @param string $moduleName
     * @param callable|null $callback
     * @return boolean
     */
    public static function has(string $moduleName, callable $onSuccess = null, callable $onFailure = null):bool {

        //ConfigCollection has Priority
        if(isset(Config::get('modules')[$moduleName])) {
            if(file_exists(self::DIRECTORY_MODULES.$moduleName.'.disabled.php')) {
                unlink(self::DIRECTORY_MODULES.$moduleName.'.disabled.php');
            }            
            if(is_callable($onSuccess)) $onSuccess(Config::get('modules')[$moduleName]);
            return true;
        }

        if(self::exists($moduleName)) {
            if(is_callable($onSuccess)) $onSuccess(self::getModulePath($moduleName));
            return true;
        } else {
            if(is_callable($onFailure)) $onFailure(self::getModulePath($moduleName));
            return false;
        }
    }

    public static function hasAny(array $modulesNames, callable $onSuccess = null, callable $onFailure = null):bool {
        foreach($modulesNames as $moduleName) {
            if(self::has($moduleName, $onSuccess, $onFailure)) return true;
        }
        return false;        
    }

    public static function hasAll(array $modulesNames, callable $onSuccess = null, callable $onFailure = null):bool {
        foreach($modulesNames as $moduleName) {
            if(!self::has($moduleName, $onSuccess, $onFailure)) return false;
        }
        return true;
    }

    private static function getModulePath(string $moduleName):string {
        if(file_exists(self::DIRECTORY_MODULES.$moduleName.'.disabled.php')) {
            return self::DIRECTORY_MODULES.$moduleName.'.disabled.php';
        } else
        if(file_exists(self::DIRECTORY_MODULES.$moduleName.'.php')) {
            return self::DIRECTORY_MODULES.$moduleName.'.php';
        }
        return "";
    }

    private static function hasInDisabled(string $moduleName):bool {
        if(file_exists(self::DIRECTORY_MODULES.$moduleName.'.disabled.php')) {
            return true;
        }
        if (isset($_GET['disable']) and $_GET['disable'] == $moduleName ) {
            return true;
        }
        return false;
    }

    private static function hasInSession(string $moduleName):bool {
		if(isset($_SESSION['RBModulos'][$moduleName])) {
			return true;
		}
        return false;
    }

    private static function hasInDirectory(string $moduleName):bool {
        if(!file_exists(self::DIRECTORY_MODULES.$moduleName.'.php')) {
            touch(self::DIRECTORY_MODULES.$moduleName.'.disabled.php');
        }
		if(file_exists(self::DIRECTORY_MODULES.$moduleName.'.php')) {
            touch(self::DIRECTORY_MODULES.$moduleName.'.php');
			return true;
        }
        return false;
    }

    private static function hasInInput(string $moduleName):bool {
        if(Input::hasAnyInputFields(['temp'])) {
            $temp = Input::getFromFirstField(['temp']);
            if($temp == $moduleName) {
                $_SESSION['RBModulos'][$moduleName] = true;
                return true;
            }
        }
        if (isset($_GET['disable']) and $_GET['disable'] == $moduleName ) {
            $_SESSION['RBModulos'][$moduleName] = false;
            unset($_SESSION['RBModulos'][$moduleName]);
        }
        return false;
    }

}
