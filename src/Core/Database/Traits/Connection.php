<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Database\Model;
use RBFrameworks\Core\Database\Modelv2;
use RBFrameworks\Core\Types\PropProps;

trait Connection {


    private function resolvePrefixo(string $prefixo) {
        $this->prefixo = $prefixo;
    }

    private function resolveTabela(string $tabela) {
        if(substr($tabela, 0, strlen($this->prefixo)) == $this->prefixo) {
            $this->tabela = $tabela;
        } else {
            $this->tabela = $this->prefixo.$tabela;
        }
    }

    private function resolveModel(array $model) {
        $this->model = $model;        
        $this->modelCheckStructure();
        if(count($this->model)) { 
            $this->modelObject = new Model(PropProps::buildFromArray($this->tabela, $this->model)); 
        }
    }

    public function getPrefixo():string {
        return $this->prefixo;
    }

    public function getTabela():string {
        return $this->tabela;
    }

    public function getModel():array {
        return $this->model;
    }

    public function getModelv2():Modelv2 {
        return new Modelv2($this->model);
    }

    public function getModelObject():object {
        if(!isset($this->modelObject)) {
            $this->modelObject = new Model([
                $this->getTabela() => $this->createModel()
            ]);
        }
        return $this->modelObject;
    }

    private function createModel():array {
        return $this->table_exists() ? $this->generateModelFromTable() : [];
    }

    public function setPrefixo(string $prefixo):object {
        $this->resolvePrefixo($prefixo);
        return $this;
    }

    public function setTabela(string $tabela):object {
        $this->resolveTabela($tabela);
        return $this;
    }

    public function setModel(array $model):object {
        $this->resolveModel($model);
        return $this;
    }

}