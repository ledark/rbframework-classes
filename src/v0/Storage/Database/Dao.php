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
        $fields = \RBFrameworks\Utils\Arrays::extractFields($dados);
        $bindedValues = \RBFrameworks\Utils\Arrays::extractBindNamedParams($dados);
        $values = \RBFrameworks\Utils\Arrays::extractValuesAsArray($dados);
        $query = "INSERT INTO `{$this->getTabela()}` ($fields) VALUES ($bindedValues)";
        return $this->query($query, $dados);
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
        $table          = $this->getTabela();
        $keys_where     = \RBFrameworks\Utils\Arrays::extractWhereAnd($keys);
        $update_values  = \RBFrameworks\Utils\Arrays::extractUpdateRaw($dados);
        $campos         = \RBFrameworks\Utils\Arrays::extractFields($dados);
        $values         = \RBFrameworks\Utils\Arrays::extractValues($dados);
        
        $query = "UPDATE `$table` SET $update_values WHERE $keys_where; INSERT INTO `$table` ($campos) SELECT $values FROM `$table` WHERE $keys_where HAVING COUNT(*) = 0 ";
        return $this->Database->setQuery($query)->execute()->rowCount();
    }

    public function getModelObject(array $model = []): object {
        if(!count($model) and isset($this->model)) $model = $this->model;
        return new \RBFrameworks\Storage\Database\Model([$this->getTabela() => $model]);
    }

    use DaoTable;    
    
}
