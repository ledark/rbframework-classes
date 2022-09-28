<?php

namespace RBFrameworks\Request;

class Request {
    
    protected function getProtocol():string {
        return isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'].'://' : 'http://';
    }
    protected function getHost():string {
        return $_SERVER['HTTP_HOST'];
    }
    protected function getApplicationPath() :string {
        $paths = explode('/', $_SERVER['SCRIPT_NAME']);
        array_pop($paths);
        return implode('/', $paths).'/';
    }
    public function getRequest():string {
        $request = str_replace($this->getApplicationPath(), '', $_SERVER['SCRIPT_URL']);
        return (empty($request)) ? '/' : $request;
    }
    public function getHttpRequest():string {
        return isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : $this->getProtocol().$this->getHost().$this->getApplicationPath().$this->getRequest();
    }
    public function getFullRequest():string {
        return str_replace($this->getApplicationPath(), '', $_SERVER['REQUEST_URI']);
    }
    public function getHttpFullRequest():string {
        return isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : $this->getProtocol().$this->getHost().$this->getApplicationPath().$this->getFullRequest();
    }
    public function getHttpSite():string {
        return $this->getProtocol().$this->getHost().$this->getApplicationPath();
    }
    public function getHttpHost():string {
        return $this->getApplicationPath();
    }
    
    
    //phpInput
    
    private $input = [];
    private $input_filter = [];
    
    public function setInputFilter(array $fields) {
        $this->input_filter = array_merge($this->input_filter, $fields);
        return $this;
    }


    public function getInput():array {
        $request_file = file_get_contents("php://input");
        if($request_file !== false) {
            $jsonrequest = json_decode($request_file);
            $this->input = (!is_null($jsonrequest)) ? get_object_vars($jsonrequest) : array();
        }
        return $this->input;
    }
    
    public function getInputEscaped():array {
        if(!count($this->input)) $this->getInput();
        $_INPUT = [];
        foreach($this->input as $chave => $valor) {
            $_INPUT[$chave] = is_string($valor) ? str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $valor) : $valor;
        }
        return $_INPUT;
    } 
    
}