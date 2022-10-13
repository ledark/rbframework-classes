<?php

namespace RBFrameworks\Helpers\Php;

use RBFrameworks\Helpers\Php\InputHandler as Handler;

class Input {
    
    protected $Handler;
    
    public function __construct(string $handler = 'auto') {
        $this->Handler = new Handler($handler);
        return $this;
    }
    
    public function getDados():array {
        return $this->Handler->getDados();
    }
    
    public function getType():string {
        return $this->Handler->type;
    }
    
    
    
    
    
    public function decodeUTF8(): object {
        $type = $this->Handler->getType();
        if(!isset($this->Handler->dados[$type])) {
            throw new \Exception("Handler não conseguiu lidar com input $type");
        }
        utf8_decode_deep($this->Handler->dados[$type]);
        return $this;
    }
    public function encodeUTF8(): object {
        $type = $this->Handler->getType();
        if(!isset($this->Handler->dados[$type])) {
            throw new \Exception("Handler não conseguiu lidar com input $type");
        }
        utf8_encode_deep($this->Handler->dados[$type]);
        return $this;
    }

/*




    
        
        
        
        plugin("request");
        $this->setDados(request(), 'phpinput');
        if($this->isEmpty()) {
            $this->setDados($_POST, 'post');
        } else
        if($this->isEmpty()) {
            $this->setDados($_SESSION, 'session');
        } else
        if($this->isEmpty()) {
            $this->setDados($_GET, 'get');
        }
    }
    

    public function getType():string {
        return $this->type;
    }

    
    public function setDados(array $dados, string $type = 'private'): object {
        $this->type = $type;
        $this->dados = $dados;
        return $this;
    }
    
    public function setType(): object {
        
    }    
    
    private function isEmpty():bool {
        return count($this->dados) ? true : false;
    }
    */
    
    public static function get():array {
        return (new self)->getDados();
    }
    
    public static function getEncoded():array {
        $dados = self::get();
        utf8_encode_deep($dados);
        return $dados;
    }
    
    public static function getDecoded():array {
        $dados = self::get();
        utf8_decode_deep($dados);
        return $dados;
    }
    
}
