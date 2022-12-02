<?php

namespace RBFrameworks\Core\Database\Model;

class UserDadosMock {

    public static function getModel():array {
		return [
			'cod' => [
				'mysql' => 'int(10) unsigned NOT NULL PRIMARY auto_increment',
				'label' => 'Cod',
				'default' => '',
				'asKey' => true,
			],
			'id_vended' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Id_vended',
				'default' => '',
			],
			'tipo' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Tipo',
				'default' => '',
			],
			'cpf' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Cpf',
				'default' => '',
			],
			'cnpj' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Cnpj',
				'default' => '',
			],
			'razao' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Razao',
				'default' => '',
			],
			'contato' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Contato',
				'default' => '',
			],
			'nome' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Nome',
				'default' => '',
			],
			'email' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Email',
				'default' => '',
			],
			'login' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Login',
				'default' => '',
			],
			'senha' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Senha',
				'default' => '',
			],
			'niver' => [
				'mysql' => 'varchar(10) NOT NULL ',
				'label' => 'Niver',
				'default' => '',
			],
			'sexo' => [
				'mysql' => 'varchar(1) NOT NULL ',
				'label' => 'Sexo',
				'default' => '',
			],
			'indicacao' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Indicacao',
				'default' => '',
			],
			'insc_est' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Insc_est',
				'default' => '',
			],
			'tipo_user' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Tipo_user',
				'default' => '',
			],
			'ie' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Ie',
				'default' => '',
			],
			'cnae' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Cnae',
				'default' => '',
			],
			'site' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Site',
				'default' => '',
			],
			'fone' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Fone',
				'default' => '',
			],
			'fone2' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Fone2',
				'default' => '',
			],
			'ca' => [
				'mysql' => 'int(10) unsigned NOT NULL ',
				'label' => 'Ca',
				'default' => '',
			],
			'pa' => [
				'mysql' => 'int(10) unsigned NOT NULL ',
				'label' => 'Pa',
				'default' => '',
			],
			'on' => [
				'mysql' => 'int(10) unsigned NOT NULL ',
				'label' => 'On',
				'default' => '',
			],
			'up' => [
				'mysql' => 'int(10) unsigned NOT NULL ',
				'label' => 'Up',
				'default' => '',
			],
			'auth' => [
				'mysql' => 'varchar(255) NOT NULL ',
				'label' => 'Auth',
				'default' => '',
			],
			'status' => [
				'mysql' => 'int(10) unsigned NOT NULL ',
				'label' => 'Status',
				'default' => '',
			],

		];
	}

	public static function getModelWithFieldMysql():array {
		$res = [];
		$model = self::getModel();
		foreach($model as $field => $props) {
			if(!isset($props['mysql'])) continue;
			$res[$field] = $props['mysql'];
		}
		return $res;
	}	

	public static function getFieldList():array {
		$res = [];
		$model = self::getModel();
		foreach($model as $field => $props) {
			$res[] = $field;
		}
		return $res;
	}

	public static function filterKeys(array $input):array {
		$res = [];
		$model = self::getModel();
		foreach($model as $field => $props) {
			if(isset($props['asKey']) and $props['asKey'] === true) {
				$res[$field] = $input[$field];
			}
		}
		return $res;		
	}

}