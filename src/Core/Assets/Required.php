<?php

namespace RBFrameworks\Core\Assets;

/**
 * Legacy Required
 */

class Required
{
    /*
    public static function lib() {
        $args = func_get_args();
        foreach($args as $libname) {
            $filename = __DIR__."/class.Required/{$libname}.php";
            if(file_exists($filename)) require_once $filename;
        }
    }

    */
	public static function files() {		
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				Includes::include_conditional($arg);
			}
		}
	}
	/*
	public static function css() {
		plugin("rbincludes");
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				$oarg = $arg;
				if(!file_exists($arg)) $arg = "_app/css/{$oarg}";
				if(!file_exists($arg)) $arg = "_app/css/{$oarg}.js";
				if(!file_exists($arg)) $arg = "{$oarg}";
				include_css($arg);
			}
		}
	}	
	public static function js() {
		plugin("rbincludes");
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				$oarg = $arg;
				if(!file_exists($arg)) $arg = "_app/js/{$oarg}";
				if(!file_exists($arg)) $arg = "_app/js/{$oarg}.js";
				if(!file_exists($arg)) $arg = "{$oarg}";
				include_js($arg);
			}
		}
	}
	
	
	public static function angular($controller, $action = null) {
		global $RBFolders;
		$file = $RBFolders['dirRB'].'/'.$RBFolders['dirRaiz'].'/'.$controller.'.angular.js';
		if(!file_exists($file)) {
			file_put_contents('log/Required', date('Ymd-His')." Arquivo $file criado devido a chamada do Required\n", FILE_APPEND);
			file_put_contents($file, 
"window.angular.module('mainApp')

.controller('$controller', function(\$scope, \$http, \$sce) {
	
});");
		}
		if($action == 'START') {
			echo '<!--//START Angular '.$controller.'//-->';
			echo '<script src="{httpSite}'.$file.'" type="text/javascript" language="javascript"></script>';
			echo '<div ng-controller="'.$controller.'">';
		}
		if($action == 'END') {
			echo '</div><!--//END Angular '.$controller.'//-->';
		}
		
		
	}
	
	public static function user() {
		$args = func_get_args();
		$user_logged = Admins::is();
		$deny = true;
		if(count($args)) {
			foreach($args as $arg) {
				if($user_logged == $arg) $deny = false;
			}
		}
		if($deny) {
			Errors::get("Acesso negado.");
			exit();
		}
	}

	public static function plugins() {
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				plugin($arg);
			}
		}
	}

	public static function logics() {
		global $RBFolders;
		if(!isset($RBFolders)) $RBFolders = [];
		if(!isset($RBFolders['dirRB'])) $RBFolders['dirRB'] = '';
		if(!isset($RBFolders['dirRaiz'])) $RBFolders['dirRaiz'] = '';
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				$file = $RBFolders['dirRB'].'/'.$RBFolders['dirRaiz'].'/'.$arg.'.php';
				if(file_exists($file)) {
					$toInclude = $file;
				} else
				if(file_exists($arg)) {
					$toInclude = $arg;
				} else 
				if(file_exists($arg.'.php')) {
					$toInclude = $arg.'.php';
				} else 
				if(file_exists('_app/logic/'.$arg.'.php')) {
					$toInclude = '_app/logic/'.$arg.'.php';
				}
			}
			if(!empty($toInclude)) {
				try {
					include($toInclude);
				} catch(Exception $e) {
					file_put_contents('log/Required', date('Ymd-His')." Lógica do Arquivo ".$toInclude." não encontrada\n", FILE_APPEND);
					Errors::get($e->getMessage());
				}
			}
		}
	}

	public static function databases() {
		$args = func_get_args();
		if(count($args)) {
			foreach($args as $arg) {
				$className = ucwords($arg);
				if(!isset($GLOBALS[$className]) and class_exists($className) ) {
                    
                    $skipsearch = false;
                    $dbpathfile = DBPath.$className.'.php';
                    
                    if(file_exists($dbpathfile) and !$skipsearch) {
                        include($dbpathfile);
                        $skipsearch = true;
                    }
                    
                    //Segunda Tentativa
                    if(!$skipsearch) {
                        $dbpathfile = substr(DBPath, 0, -6).'private/class.'.$className.'.php';
                        if(file_exists($dbpathfile)) {
                            include($dbpathfile);
                            $skipsearch = true;
                        }
                    }
                    
                    //Terceira Tentativa
                    if(!$skipsearch) {
                        $dbpathfile = substr(DBPath, 0, -6).'ecom/class.'.$className.'.php';
                        if(file_exists($dbpathfile)) {
                            include($dbpathfile);
                            $skipsearch = true;
                        }
                    }

					$GLOBALS[$className] = $$className ;
				}
				if(class_exists($className)) continue;
				if(is_callable($GLOBALS[$className])) continue;
				if(empty($arg)) continue;
				if(file_exists(DBPath.$className.'.php')) {
					include(DBPath.$className.'.php');					
					$GLOBALS[$className] = $$className ;	
				}
			}
		}
	}
	
	//Alias
	public static function plugin() {
		$args = func_get_args();
		foreach($args as $arg) {
			self::plugins($arg);	
		}
	}
	public static function logic() {
		$args = func_get_args();
		foreach($args as $arg) {
			self::logics($arg);	
		}
	}
	public static function database() {
		$args = func_get_args();
		foreach($args as $arg) {
			self::databases($arg);	
		}
	}
    */
	
}
