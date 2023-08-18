<?php

namespace RBFrameworks\Database;

class Model {
    
    public $primary = '';
    public $tabela;
    public $model = [];
    public $uncaught = [];
    
    private function checkConstructor($model) {
        try {
            $tabela = key($model);
            if(!is_string($tabela)) throw new \Exception("Model no formato incorreto. Precisa ser ['tabela' => array(dados)]");
            $info = array_shift($model);
            if(!count($info)) {
                throw new \Exception("Model no formato incorreto. Precisa ser ['{$tabela}' => array('campo' => ['mysql' => 'STATEMENT_CREATE', ...], ...)");
            }
            $this
                ->setTableName($tabela)
                ->walk($info)
            ;
        } catch(\Exception $e) {
            exit();
        }
    }
    
    public function __construct(array $newModel) {        
        $this->checkConstructor($newModel);
        return $this;
    }
    
    public function setTableName(string $tablename) {
        $this->tabela = $tablename;
        return $this;
    }
    
    private function compileMysql(string $field, string $code):string {
        $code = $this->compileMysql_primary($field, $code);
        return $code;
    }
    
    private function compileMysql_primary(string $field, string $code):string {
        if(strpos($code, ' PRIMARY') !== false) {
            $code = str_replace(' PRIMARY', '', $code);
            $this->model[$field]['isPrimary'] = true;
            $this->uncaught['PRIMARY'] = $field;
            $this->primary = $field;
        }
        return $code;
    }
    
    private function walk(array $model) {
        foreach($model as $field => $params) {
            if(isset($params['mysql'])) {
                $this->model[$field] = $params;
                $this->model[$field]['mysql'] = $this->compileMysql($field, $params['mysql']);
            } else {
                $this->uncaught[$field] = $params;
            }
        }
    }
    
    /**
     * Esperado os values no formato [field => value]
     * @param array $values
     */
    public function humanize(array $values, callable $filter = null):array {
        if(is_string(key($values))) {
            $values = [0 => $values];
        }
        $result = [];
        foreach($values as $index => $row) {
            foreach($row as $field => $value) {
                if(isset($this->model[$field]) and isset($this->model[$field]['humanize'])) {
                    call_user_func_array($this->model[$field]['humanize'], [&$value, $field, &$row]);
                }
                if(is_object($filter)) {          
                   call_user_func_array($filter, [&$value, $field, &$row]);
                }
                if(is_null($value)) {
                    unset($row[$field]);
                } else {
                    $row[$field] = $value;
                }
            }
            $result[] = $row;
        }
        return $result;
    }
    
    public function getPreviousField(string $field) {
        $keys = array_keys($this->model);
        $found_index = array_search($field, $keys);
        if ($found_index === false || $found_index === 0) return false;
        return $keys[$found_index-1];
    }
    
    
}
