<?php

namespace RBFrameworks\Database;

class Model {
    
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
        } catch(Exception $e) {
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
    
    private function walk(array $model) {
        foreach($model as $field => $params) {
            if(isset($params['mysql'])) {
                $this->model[$field] = $params;
            } else {
                $this->uncaught[$field] = $params;
            }
        }
    }
    
    
}
