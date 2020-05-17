<?php

namespace RBFrameworks\Helpers\Http;

class Get extends \RBFrameworks\Helpers\Http {
    
    public function __construct(string $url, string $info = TYPE_NONE) {
        parent::__construct($url);
        
        $this
            ->setMethod("GET")
            ->setOptions()
            ->run()
        ;
        
        return $this->returnInfo($info);
    }
    
}
