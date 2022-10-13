<?php

namespace RBFrameworks\Helpers\Http;

if(!defined('TYPE_NONE')) define('TYPE_NONE', 'NONE');

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
