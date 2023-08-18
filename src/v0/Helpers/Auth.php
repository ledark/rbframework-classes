<?php

namespace RBFrameworks\Helpers;

class Auth {
    
    public $events = [];
    private $token;
    
    public function __construct(string $token = 'RBAuth') {
        $this->token = $token;
    }
    
    public function isLogged(): bool {
        if (!isset($_SESSION[$this->token])) {
            return false;
        } else{
            return true;
        }
    }
    
    public function onSuccess(callable $callback) {
        $this->events['success'] = $callback;
        return $this;
    }
    
    public function onFail(callable $callback) {
        $this->events['fail'] = $callback;
        return $this;
    }
    
    /**
     * Você informa qualquer valor que deseja usar como forma de autenticação.
     * @param type $mixedCredentials
     */
    public function setCredentials($mixedCredentials): object {
        $this->Credentials = $mixedCredentials;
        return $this;
    }
    
    /**
     * Valida as Credenciais
     */
    public function validate() {
        if($this->Credentials == 'root123') {
            $_SESSION[$this->token] = true;
            $this->trigger('success');
        } else {
            $this->trigger('fail');
        }
    }
    
    public function logout() {
        unset($_SESSION[$this->token]);
    }
    
    
    private function trigger(string $eventName) {
        if(isset($this->events[$eventName])) {
            $this->events[$eventName]();
        }
    }
    
}
