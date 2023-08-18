<?php

namespace RBFrameworks\Helpers\Collections;



class HandleCollectionsConfig {
    
    private static $instance;
    private static $collection;
    
    public function get(string $name) {
        return self::$collection[$name] ?? [];
    }
    
    public function set(string $name, $value) {
        self::$collection[$name] = $value;
    }
    
    public static function getInstance() {
        $_SESSION['collections'] ?? [];
        if(self::$instance === null){
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    protected function __construct() {
        ;
    }
    private function __clone() {
        
    }
    private function __wakeup() {
        
    }
    
}
