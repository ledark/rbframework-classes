<?php

namespace RBFrameworks\Router;

class Router {
    
    use RouterClass;
    
    public $Request;
    
    public function __construct(\RBFrameworks\Request $Request) {
        $this->Request = $Request;
    }
    
    public function searchFile(){
        if(file_exists($this->Request->getFullRequest())) {
            readfile($this->Request->getFullRequest());
            exit();
        }
        return $this;
    }

    /**
     * Como Router, o método searchClass() deverá procurar uma classe que faça match na $this->Request->getFullRequest()
     * Para isso, essa função irá transformar a $Request em tentativas
     * Se houver uma classe que combine, então executa essa classe.
     * @return $this caso não haja nenhuma classe que combine com o $Request
     */
    public function searchClass(string $prefix = 'Controllers\\', string $ClassDefault = 'Main'){
        $attempts = $this->createClassAttempts($this->Request->getFullRequest(), $prefix, $ClassDefault);
        foreach($attempts as $attempt) {
            if(!count($attempt)) continue;
            if(class_exists($attempt['ClassPrefix'].$attempt['ClassName'])) {
                $ReflectionClass = new \ReflectionClass($attempt['ClassPrefix'].$attempt['ClassName']);
                if($ReflectionClass->hasMethod($attempt['MethodName'])) {
                    $Class = $attempt['ClassPrefix'].$attempt['ClassName'];
                    $Method = $attempt['MethodName'];
                    $Params = $attempt['Params'];
                    call_user_func_array([ new $Class, $Method ], $Params);
                    exit();
                }
            }
        }
        return $this;
    }
    
    public function searchDatabase() {
        return $this;
    }
    
} 
