<?php

namespace RBFrameworks\Database;

class Dao extends \RBFrameworks\Database implements Crudable {
    
    public $tabela;

    public function __construct(string $tabela, string $databaseConfig = 'database') {
        parent::__construct($databaseConfig);
        $this->setTabela($tabela);
    }
    
    protected function setTabela(string $tabela) {
        $this->tabela = $this->prefixo.str_replace($this->prefixo, '', $tabela);
    }
    
    protected function getTabela(bool $withPrefix = true):string {
        return $withPrefix ? $this->tabela : str_replace($this->prefixo, '', $this->tabela);
    }
    
    public function add(array $dados) {
        
    }
    
    public function set(array $dados, array $keys) {
        
    }
    
    public function del(array $keys) {
        
    }
    
    public function get() {
        
    }
    
}
