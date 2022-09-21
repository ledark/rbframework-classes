<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QueryVariables {   

    public $tables = array(); //Alias para os nomes das tabelas
    public $fields = array();
    public $clauses = array(); //Array de Condições para Execução da Query
    public $groupby = null;  
    public $from = null;
    
    public $limit_min = 0;
    public $limit_max = 0;
    private $limit = null;
    private $order = null;
    private $alfabeto;
    public $type = "SELECT";
    private $logfolder = "log/cache/";
    public $name = "query"; //nome para fins de log

    //Prefix

    public function getPrefixo():string {
        return isset($this->prefixo) ? $this->prefixo : '';
    }

    public function setPrefixo(string $prefixo):object {
        $this->$prefixo = $prefixo;
        return $this;
    }
}