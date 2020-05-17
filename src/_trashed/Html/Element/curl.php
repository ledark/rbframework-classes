<?php

namespace RBFrameworks\Html\Element;

class curl {
    
    private $html = '';


    public function __construct(string $href) {
        
        $curl = new \RBFrameworks\Helpers\Http\Get($href);
        $this->html = $curl->render();
    }
    
    public function render() {
        return $this->html;
    }
    
     
}
