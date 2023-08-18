<?php

namespace RBFrameworks\Helpers\Php;

class InputHandler {
    
    public $dados = [
        'private' => [],
        'phpinput' => [],
        'post' => [],
        'session' => [],
        'get' => [],
    ];
    public $type = 'auto';
    
    public function setPrivate(array $dados): object {
        $this->dados['private'] = $dados;
        return $this;
    }
    public function setPhpinput(): object {
        plugin("request");
        $this->dados['phpinput'] = request();
        return $this;
    }
    public function setPost(): object {
        $this->dados['post'] = $_POST;
        return $this;
    }
    public function setSession(): object {
        $this->dados['session'] = $_SESSION;
        return $this;
    }
    public function setGet(): object {
        $this->dados['get'] = $_GET;
        return $this;
    }
    
    public function getPrivate():array {
        return $this->dados['private'];
    }
    public function getPhpinput():array {
        return $this->dados['phpinput'];
    }
    public function getPost():array {
        return $this->dados['post'];
    }
    public function getSession():array {
        return $this->dados['session'];
    }
    public function getGet():array {
        return $this->dados['get'];
    }
    
    public function __construct(string $type) {
        $this
            ->handleType($type)
        ;
        
    }
    



    public function handleTypeAuto(): string {
        $attempts = ['phpinput', 'post', 'session', 'get'];
        foreach($attempts as $attempt) {
            $this->handleType($attempt);
            $dados = call_user_func([$this, 'get'.ucfirst($attempt)]);
            if(count($dados)) {
                $this->type = $attempt;
                return $this->type;
            }
            return 'auto';
        }
        
        $dados = $this->setPhpinput()->getPhpinput();
        if(count($dados)) {
            $this->type = 'phpinput';
        } else {
            
        }
    }
    
    public function handleType(string $type): object {
        if($type == 'auto') {
            $this->handleTypeAuto();
        } else
        if($type == 'private') {
            $this->type = $type;
        } else
        if(in_array($type, array_keys($this->dados))) {
            $this->type = $type;
            call_user_func([$this, 'set'.ucfirst($type)]);
        } else {
            throw new \Exception("InputHandler Type $type undefined.");
        }
        return $this;
    }    
    
    public function getDados():array {
        return call_user_func([$this, 'get'.ucfirst($this->type)]);
    }
    
    public function getType():string {
        return $this->type;
    }
    
    /*
    private $types = ['auto', 'private', 'phpinput', 'post', 'get', 'session'];
    public $type = 'private';
    

    

    
    public function handleType(): object {
        
        switch($this->type) {
            case 'auto':
                
            break;
            case 'private':
                
            break;
            case 'phpinput':
                
            break;
            case 'post':
                
            break;
            case 'get':
                
            break;
            case 'session':
                
            break;
        }
        return $this;
    }
    


    */
}
