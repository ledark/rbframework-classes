<?php

namespace RBFrameworks\Core\Auth;

use RBFrameworks\Core\Auth;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Database\Modelv2;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Input;
use RBFrameworks\Core\Response;
use RBFrameworks\Core\Response\Mock\Common as ResponseMockCommon;

class AdminsTokens extends Database {

    public $token;

    public function __construct() {        

        $this->model = [
            'id' => ['mysql' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,'],
            'created_time' => ['mysql' => 'INT(10) UNSIGNED NULL,'],
            'access_time' => ['mysql' => 'INT(10) UNSIGNED NULL,'],
            'token' => ['mysql' => 'VARCHAR(255) NOT NULL ,'],
            'data' => ['mysql' => 'LONGTEXT NOT NULL ,'],
            'status' => ['mysql' => 'VARCHAR(50) NOT NULL ,'],
        ];

        $this->tabela = Config::get('database.prefixo').'admins_tokens';
        $this->token = null;

        $model = new Modelv2($this->model);

        parent::__construct($this->tabela, $model->getModelFldSql());

    }

    public function getToken():string {
        if(isset($this->token) and is_string($this->token)) return $this->token;
        Plugin::load("session");
        return session_admin_get_token();
    }

    public function setToken(string $token):object {
        $this->token = $token;
        return $this;
    }

    public function getStatus(string $token = null):string {
        $token = is_null($this->token) ? $token : $this->token;
        $has = $this->queryFirstRow("SELECT `status` FROM {$this->tabela} WHERE token=%s", $token);
        return (is_array($has)) ? $has['status'] : 'invalid';
    }

    public function isActive(string $token = null):bool {
        $token = is_null($this->token) ? $token : $this->token;
        $status = $this->getStatus($token);
        return ( in_array($status, ['created', 'active']) ) ? true : false;
    }

    public function create(array $dados) {

        //$token = uniqid(md5(serialize($dados)));
        if(function_exists('session_init')) {
            $dados['session_id'] = session_id();
        }
        
        $token = Auth::generateToken(base64_encode(serialize($dados)));
        $this->token = $token;
        
        $this->insert($this->tabela, [
            'created_time' => time(),
            'access_time' => time(),
            'token' => $token,
            'data' => json_encode($dados),
            'status' => 'active',
        ]);

        return $token;

    }

    public function inactive(string $token = null) {
        $token = is_null($this->token) ? $token : $this->token;
        $this->update($this->tabela, [
            'status' => 'inactive',
        ], "token=%s", $token);

    }

    public function revalidate(string $token = null) {
        $token = is_null($this->token) ? $token : $this->token;
        $this->update($this->tabela, [
            'access_time' => time(),
        ], "token=%s", $token);

    }

    public function logInto(string $token = null, string $message = '', array $context = [], bool $forceUTF8 = true) {

        if($forceUTF8) {
            if(json_encode($context) === false) {
                Encoding::DeepEncode($context);
            }
            if(json_encode($context) === false) {
                Encoding::DeepDecode($context);
            }
        }
        
        $token = is_null($this->token) ? $token : $this->token;
        $tokensHistory = new Database('admins_tokens_history');
        $tokensHistory->insert( Config::get('database.prefixo'). 'admins_tokens_history', [
            'token' => $token,
            'on' => time(),
            'message' => $message,
            'context' => json_encode($context) === false ? serialize($context) : json_encode($context),
        ]);

    }

    public function needsValidation(string $message = 'granted access', array $context =[]) {
        if(is_null($this->token)) {
            $token = is_null(Input::getAll()['token']) ? (new Input())->phpSessionGet('token', 'invalid-token') : get_input()['token'];
        } else {
            $token = $this->token;
        }
        if($this->isActive($token)) {
            $this->revalidate($token);
            $this->logInto($token, $message, $context);
        } else {

            $this->logInto($token, '[NEGADO]'.$message, $context);

            //\Core\Plugin::load('response');
            Response::json( ResponseMockCommon::getMockError("needs revalidate") );
            
        }
    }

    public function __toString() {
        return is_null($this->token) ? 'has-no-token-to-get' : $this->token;
    }


}