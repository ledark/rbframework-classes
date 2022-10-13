<?php

namespace RBFrameworks;

use RBFrameworks\Request;

class Config {
    
    public $configs = [];
    
    public function __construct() {
        $this->configs = include('config.php');
        $this->setConstantsRB();
    }
    
    public function setConstantsRB() {
        $this->configs['constants'] = array_merge($this->configs['constants'], [
            'HTTPSITE' => (new Request())->getHttpSite(),
        ]);
    }
    
    public function extract(string $group = '') {
        return (empty($group)) ? $this->configs : $this->configs[$group];
    }
    
    public function getConstants() {
        foreach($this->configs['constants'] as $name => $value) {
            if(!defined($name)) {
                define("{$name}", $value);
            }
        }
    }
    
}
