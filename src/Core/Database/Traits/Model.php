<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Database\Modelv2;

trait Model {

    private function modelCheckStructure() {
        if(!$this->hasModel()) return false;
        if( $this->getNumDimensions($this->model) < 1 ) throw new \Exception("Model invalido");
        if( $this->getNumDimensions($this->model) == 1 ) $this->modelCheckStructure_asSimple();
        if( $this->getNumDimensions($this->model) == 2 ) $this->modelCheckStructure_asLazy();
        $tabela = key($this->model);
        if(!is_string($tabela)) {
            throw new \Exception("Model no formato incorreto. Precisa ser ['tabela' => array(dados)] get: ".\Core\Debug::getPrintableAsText($this->model));
        }
        $model = $this->model;
        $info = array_shift($model);
        if(!count($info)) throw new \Exception("Model no formato incorreto. Precisa ser ['{$tabela}' => array('campo' => ['mysql' => 'STATEMENT_CREATE', ...], ...)");  
        $this->generateMysqlAndUncaught($info);
    }

    private function modelCheckStructure_asSimple() {
        $this->model = [ $this->getTabela() => $this->model ];
    }

    private function modelCheckStructure_asLazy() {
        $tabela = key($this->model);
        $modelOri = $this->model[$tabela];
        $model = [];
        foreach($modelOri as $chave => $value) {
            if(is_string($value)) {
                $model[$chave] = ['mysql' => $value];
            } else if(is_array($value)) {
                $model[$chave] = $value;
            }
        }
        $this->model = [ $this->getTabela() => $model ];
    }

    public function hasModel():bool {
        return count($this->model) ? true : false; 
    }

    private function generateMysqlAndUncaught(array $model) {
        foreach($model as $field => $params) {
            if(isset($params['mysql'])) {
                $this->model[$field] = $params;
            } else {
                $this->uncaught[$field] = $params;
            }
        }
    }
    
    /**
     * Return Model compativel com v2
     */
    public function getModelFldSql(bool $strict = false):array {
        $m = $this->getModelObject()->getModel();
        if(key_exists($this->getTabela(), $m)) {
            $m = reset($m);
        }
        $Model = new Modelv2($m);
        return $Model->getModelFldSql();
    }

    public function getModelFldPrp() {
        $m = $this->getModelObject()->getModel();
        if(key_exists($this->getTabela(), $m)) {
            $m = reset($m);
        }
        $Model = new Modelv2($m);
        return $Model->getModel();
    }
    
}