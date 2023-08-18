<?php

namespace RBFrameworks\Helpers\Http;

if(!defined('TYPE_NONE')) define('TYPE_NONE', 'NONE');

class GetCached extends \RBFrameworks\Helpers\Http {
    
    public function __construct(string $url, string $info = TYPE_NONE) {
        
        /**
         * Essa função verifica se toda a resposta para a URL já existe, e caso exista retorna a resposta.
         */
        $this->cacheexpires = 60*60;
        
        if(file_exists(md5($url))) {
            
        }
        
        parent::__construct($url);
        
        $this
            ->setMethod("GET")
            ->setOptions()
            ->run()
        ;
        
        return $this->returnInfo($info);
    }
    
}
