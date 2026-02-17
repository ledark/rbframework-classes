<?php

namespace RBFrameworks\Core\Auth;

use RBFrameworks\Core\Database;
use RBFrameworks\Core\Database\SQLGetter;

class AdminsRoles extends Database {

    public function __construct(string $tabela = 'admins_tipos', array $model = [], $config = null) {

        if(!count($model)) $model = [
            'tipo'          => ['mysql' => 'VARCHAR(50) NOT NULL'],
            'role_key'      => ['mysql' => 'VARCHAR(50) NOT NULL'],
            'role_value'    => ['mysql' => 'LONGTEXT NOT NULL'],
        ];
        parent::__construct($tabela, $model, $config);

    }

    public function setCod(int $cod_admin) {
        $this->cod_admin = $cod_admin;
    }

    public function getCod():int {
        return isset($this->cod_admin) ? $this->cod_admin : 0;
    }

    private function getQueryRoles(int $cod_admin = null):string {
        $cod_admin = is_null($cod_admin) ? $this->getCod() : $cod_admin;
        return SQLGetter::query("SELECT `role_key`, `role_value` FROM `{prefixo}admins` LEFT JOIN `{prefixo}admins_tipos` ON `{prefixo}admins`.tipo = `{prefixo}admins_tipos`.tipo WHERE `{prefixo}admins`.cod = {$cod_admin}");
    }

    public function getRoles():array {
        if(isset($this->roles)) return $this->roles;
        $roles = [];
        $roles_result = $this->query($this->getQueryRoles());
        foreach($roles_result as $i => $r) {
            $roles[$r['role_key']] = $r['role_value'];
        }
        $this->roles = $roles;
        return $this->roles;
    }    

    public function hasRole(string $key):bool {
        return in_array($key, array_keys($this->getRoles()));
    }

    public function getRole(string $key) {
        return $this->hasRole($key) ? $this->getRoles()[$key] : null;
    }

    public function assertRole(string $key, string $value = null):bool {
        if(is_null($value)) {
            return !is_null($this->getRole($key)) ? true : false;
        } else {
            return ($this->getRole($key) == $value) ? true : false;
        }
        return false;
    }

}
