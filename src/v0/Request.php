<?php

namespace RBFrameworks;

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
    
}