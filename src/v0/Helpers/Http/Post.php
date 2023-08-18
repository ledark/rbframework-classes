<?php

namespace RBFrameworks\Helpers\Http;

class Post extends \RBFrameworks\Helpers\Http {
    
    public function __construct(string $url, string $info = TYPE_NONE) {
        parent::__construct($url);
        
        $this
            ->setMethod("POST")
            ->setOptions()
            ->run()
        ;
        
        return $this->returnInfo($info);
        
    }
    

    
}