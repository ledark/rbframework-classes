<?php

namespace RBFrameworks\Helpers\Collections;

use RBFrameworks\Helpers\Php\Arrays as ArrayUtils;
use RBFrameworks\Helpers\Collections as Collections;

class HandleCollections {
      
    protected $handlers = ['storage', 'session', 'memory', 'private', 'globals'];
    public $handler = 'private';
    
    public function __construct(string $handlerName) {
        $this->handler = (in_array($handlerName, $this->handlers)) ? $handlerName : 'storage';
    }
    
    public function getHandler(): string {
        return $this->handler;
    }
       
    public function init(Collections $collection):array {
        $method = 'init'.ucfirst($this->getHandler());
        return call_user_func_array([$this, $method], [$collection]);
    }
    
    public function save(Collections $collection) {
        $method = 'save'.ucfirst($this->getHandler());
        call_user_func_array([$this, $method], [$collection]);
    }
    
    public function get(Collections $collection):array {
        $method = 'get'.ucfirst($this->getHandler());
        return call_user_func_array([$this, $method], [$collection]);
    }
    
    use Handler\traitInit;
    use Handler\traitSave;
    use Handler\traitGet;
    

    

    
}
