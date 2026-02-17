<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Config;

/*
	Desenvolvido por Ricardo[at]Bermejo[.com.br]
	Modulos vr. 2.0.230818
	--------------------------------
	Utilização:
	--------------------------------
	
	Na função has você configura todos os módulos já criados, fazendo-os retornar como true sempre que chamados, ao menos que queria desativar algum.
	Para módulos experimentais, você pode retornar true apenas se uma variavel $_GET for chamada, por exemplo.
	
	return boolean	Modulos::has('name');
	return void		Modulos::disable('name');
	return string	Modulos::config('name', 'config');
	
	--------------------------------
	use em cada bloco novo de código:
	if(Modulos::has('cores')) {
		Code...
	}
	--------------------------------
*/
class Modulos {

	private static function init():void {
		if(!isset($_SESSION[self::getSessionName()])) {
			$_SESSION[self::getSessionName()] = [];
		}
	}

	private static function getSessionName():string {
		return Config::assigned('modules.config.name', "RBModulos");
	}

	private static function modulesDirectory():array {
		$customDirectories = Config::assigned('modules.config.directories', []);
		return array_merge([
			__DIR__."/class.Modulos/[MODULE_NAME].php",
			"_app/class/[MODULE_NAME].php",
		], $customDirectories);
	}

	/**
	 * Modulos::has() function return true if a module is active or false if not.
	 *
	 * @param string $name module
	 * @return boolean
	 */
	public static function has(string $name):bool {

		self::init();

		//Verifica se existe nas collections
		$inConfig = Config::assigned('modules.'.$name, false);
		if(is_bool($inConfig)) {
			return $inConfig;
		}
		
		//Use a variável ?disable= para desativar um módulo
		if ( isset($_GET['disable']) and $_GET['disable'] == $name ) {
			unset($_SESSION[self::getSessionName()][$name]);
			return false;
		}
		
		//Verifica se existe no arquivo class.Modulos/NOME.php para ser sempre ativo
		foreach(self::modulesDirectory() as $dir) {
			if(file_exists( str_replace('[MODULE_NAME]', $name, $dir) )) {
				return true;
			}
		}
		
		//Verifica se existe na sessão
		if(isset($_SESSION[self::getSessionName()]) and isset($_SESSION[self::getSessionName()][$name])) {
			return true;
		}
		
		//Verifica se existe na variável ?temp=
		if ( isset($_GET['temp']) and $_GET['temp'] == $name ) {
			return true;
		}
		
		return false;
	}
	/*
	Deprecate
	*/
	public static function disable($name) {
		unset($_SESSION[self::getSessionName()][$name]);
	}

	public static function getConfig(string $name):array {
		$collectionConfig = Config::assigned('modules.'.$name, []);
		foreach(self::modulesDirectory() as $dir) {
			$moduleFile = str_replace('[MODULE_NAME]', $name, $dir);
			if(file_exists( $moduleFile )) {
				$config = include($moduleFile);
			}
		}
		$customConfig = is_array($config) ? $config : [];
		return array_merge($collectionConfig, $customConfig);
	}
}