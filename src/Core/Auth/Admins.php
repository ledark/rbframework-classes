<?php

namespace RBFrameworks\Core\Auth;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Response\Mock\Auth as ResponseMockAuth;
use RBFrameworks\Events\Event;
use RBFrameworks\Events\EventDispatcher;

class Admins extends Database {

    public function __construct(string $tabela = 'admins', array $model = [], $config = null) {

        if(!count($model)) $model = [
            'cod'       => ' INT(10) UNSIGNED NOT NULL PRIMARY AUTO_INCREMENT',
            'nome'      => ' VARCHAR(255) NOT NULL ',
            'email'         => ' VARCHAR(255) NOT NULL UNIQUE',
            'login'         => ' VARCHAR(255) NOT NULL UNIQUE',
            'senha'         => ' VARCHAR(255) NOT NULL ',
            'pa'        => ' INT(10) UNSIGNED NOT NULL',
            'aa'        => ' INT(10) UNSIGNED NOT NULL',
            'ua'        => ' INT(10) UNSIGNED NOT NULL',
            'ca'        => ' INT(10) UNSIGNED NOT NULL',
            //'old_senha'         => ' INT(10) UNSIGNED NOT NULL',
            'cod_sess'      => ' VARCHAR(255) NOT NULL ',
            'tipo'      => ' VARCHAR(255) NOT NULL ',
            'ip'        => ' VARCHAR(255) NOT NULL ',            
        ];
        parent::__construct($tabela, $model, $config);
    }

    public function doAuth(array $inputUser, callable $sucess, callable $fail):array {

        if(!isset($inputUser['login'])) return $fail(ResponseMockAuth::getMockError("Campo Login ausente"));
        if(!isset($inputUser['senha'])) return $fail(ResponseMockAuth::getMockError("Campo Senha ausente"));
        if(empty($inputUser['login'])) return $fail(ResponseMockAuth::getMockError("Por favor, preencha o seu Login"));
        if(empty($inputUser['senha'])) return $fail(ResponseMockAuth::getMockError("Por favor, preencha a sua senha"));

        $has = $this->queryFirstRow("SELECT * FROM {$this->getTabela()} WHERE login=%s_login AND senha=%s_senha", $inputUser);
        if(is_array($has)) {

            $_SESSION[Config::get('session.admin.name')]['data'][0] = $has;
            //EventDispatcher::fire( new Event("admin", "login.success") );
            return $sucess(ResponseMockAuth::getMockSuccess("Autenticado com Sucesso"));
        } else {

            $hasUser = $this->queryFirstRow("SELECT `cod` FROM {$this->getTabela()} WHERE login=%s_login", $inputUser);
            if(is_array($hasUser)) {
                //EventDispatcher::fire( new Event("admin", "login.fail", ['loginExists' => true]) );
                return $fail(ResponseMockAuth::getMockError("Senha inválida para acesso ao sistema."));
            } else {
                //EventDispatcher::fire( new Event("admin", "login.fail", ['loginExists' => false]) );
                return $fail(ResponseMockAuth::getMockError("Login ou Senha Inválidos"));
            }

        }
    }

	public static function is() {
		$args = func_get_args();
		if(count($args)) {
			$return = false;
			foreach($args as $arg) {
				if( $_SESSION[Config::get('session.admin.name')]['data'][0]['tipo'] == $arg ) $return = true;
			}
			return $return;
		} else {
			return $_SESSION[Config::get('session.admin.name')]['data'][0]['tipo'];
		}
	}
	
	public static function show($var) {
		return $_SESSION[Config::get('session.admin.name')]['data'][0][$var];
    }
    
    public function getRoles():array {
        if(isset($this->roles)) return $this->roles;
        $cod_admin = self::show('cod');
        $this->roles = $this->query("SELECT `role_key`, `role_value` `FROM {prefixo}admins` LEFT `JOIN {prefixo}admins_tipos` ON `{prefixo}admins`.tipo = `{prefixo}admins_tipos`.tipo WHERE `{prefixo}admins`.cod = {$cod_admin}");
        return $this->roles;
    }

    public function getRandomUserCod():int {
        $cod = $this->queryFirstField("SELECT cod FROM ?_admins");
        if(is_null($cod)) {
            throw new \Exception("Nenhum usuário encontrado na Base de Admins");
        }
        return $cod;
    }

}