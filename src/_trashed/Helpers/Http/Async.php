<?php

namespace RBFrameworks\Helpers\Http;

/**
 * Não testado, a ideia é usar os exemplos do manual do php para curl_multi_init
 * Note que não são de fato asyncronos, já que o PHP espera o request mais longo terminar para continuar...
 * A solução seria usar o ptreads
 */
class Async {
    
    protected $MultiHandler = false;
    public $cURLResources = [];

    public function __construct() {
        $this->MultiHandler = new curl_multi_init();
        if($this->MultiHandler === false) throw new \Exception("Não foi possível iniciar o modo Async do cURL");
    }
    
    public function addProcess( $cURLResource, string $name = '' ): object {
        $name = !empty($name) ? $name : 'resource'.count($this->cURLResources);
        $this->cURLResources[$name] = $cURLResource;
        if(!is_resource($cURLResource)) throw new \Exception("Argumento para o construtor do Async precisa ser um resource cURL válido"); 
        curl_multi_add_handle($this->MultiHandler, $this->cURLResources[$name]);
        return $this;
    }
       
    public function run() {
        $active = null;
        do {
            $status = curl_multi_exec($this->MultiHandler, $active);
            if ($active) {
                curl_multi_select($this->MultiHandler);
            }
        } while ($active && $status == CURLM_OK);
        return $this;
    }
    
    public function getResponses():array {
        $res = [];
        foreach($this->cURLResources as $name => $resource) {
            $res[$name] = curl_multi_getcontent($resource);
        }
        return $res;
    }
    
    public function __destruct() {
        //close the handles
        foreach($this->cURLResources as $name => $resource) {
            curl_multi_remove_handle($this->MultiHandler, $resource);
        }
        curl_multi_close($this->MultiHandler);
    }
    
}
