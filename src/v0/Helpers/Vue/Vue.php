<?php

namespace RBFrameworks\Helpers\Vue;

class Vue {
    
    protected $element;
    public $data = [];

    public function __construct(string $element) {
        $this->element = substr($element, 0, 1) == '#' ? $element : '#'.$element;
        return $this;
    }
    
    public function setData(array $dados) {
        foreach($dados as $chave => $valor) {
            $this->set($chave, $valor);
        }
        return $this;
    }
    
    public function set(string $chave, $valor) {
        $this->data[$chave] = $valor;
        return $this;
    }
    
    public static function cdn($type = 'producao'):string {
        return ($type == 'producao') ? 'https://cdn.jsdelivr.net/npm/vue' : 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js';
    }
    
    public function render() {
        
    }
}
