<?php

namespace RBFrameworks\Storage\Database;

class Dao extends \RBFrameworks\Storage\Database implements \RBFrameworks\Interfaces\Crudable {
    
    public $tabela;

    public function __construct(string $tabela, string $databaseConfig = 'database') {
        parent::__construct($databaseConfig);
        $this->setTabela($tabela);
    }
    
    protected function setTabela(string $tabela) {
        $this->tabela = $this->prefixo.str_replace($this->prefixo, '', $tabela);
    }
    
    public function getTabela(bool $withPrefix = true):string {
        return $withPrefix ? $this->tabela : str_replace($this->prefixo, '', $this->tabela);
    }

    public function add(array $dados) {
        
    }

    public function del(array $keys) {
        
    }

    public function get(array $dados, array $criterias) {
        $fields = \RBFrameworks\Utils\Arrays::extractFields($dados);
        $criterias = \RBFrameworks\Utils\Arrays::extractWhereAnd($criterias);        
        return $this->query("SELECT $fields FROM `{$this->getTabela()}` WHERE (1=1) AND $criterias");
    }

    public function set(array $dados, array $keys) {
        
    }

    public function upsert(array $dados, array $keys) {
        
    }

}
