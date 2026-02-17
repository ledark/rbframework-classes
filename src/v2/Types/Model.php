<?php 

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Database\Modelv2;

class Model {

    public $model;

    public function __construct($model) {
        $this->model = $model;
    }

    /**
     * return [
     *  'field1' => ['label' => 'a', 'mysql' => 'longtext not null'],
     *  'field2' => ['label' => 'a', 'mysql' => 'longtext not null'],
     * ]
     */
    public function getFldPrp():array {
        throw new \Exception("Not implemented transformations to FldPrp from ".$this->getType());
        switch($this->getType()) {
            case '[Tab->Fld->Sql]':
                
            break;
            case '[Tab->Fld->Prp]':
            
            break;
            case '[Mod->???]':
                throw new \Exception("Not implemented transformation on [Mod->???]");
            break;
            case '[Fld->Sql]':
            
            break;
            case '[Fld->Prp]':
            
            break;
        }
    }

    /**
     * return [
     *  'tablename' => [
     *      'field1' => ['label' => 'a', 'mysql' => 'longtext not null'],
     *      'field2' => ['label' => 'a', 'mysql' => 'longtext not null'],
     *  ]
     * ]
     */    
    public function getTabFldPrp():array {
        throw new \Exception("Not implemented transformations to TabFldPrp from ".$this->getType());
        switch($this->getType()) {
            case '[Tab->Fld->Sql]':
                
            break;
            case '[Tab->Fld->Prp]':
            
            break;
            case '[Mod->???]':
                throw new \Exception("Not implemented transformation on [Mod->???]");
            break;
            case '[Fld->Sql]':
            
            break;
            case '[Fld->Prp]':
            
            break;
        }
    }

    /**
     * return [
     *  'field1' => 'longtext not null',
     *  'field2' => 'longtext not null',
     * ]
     */        
    public function getFldSql():array {
        switch($this->getType()) {
            case '[Tab->Fld->Sql]':
            
            break;
            case '[Tab->Fld->Prp]':
            
            break;
            case '[Mod->???]':
                throw new \Exception("Not implemented transformation on [Mod->???]");
            break;
            case '[Fld->Sql]':
            
            break;
            case '[Fld->Prp]':
                $fldsql = [];
                foreach($this->model as $field => $props) {
                    if(!isset($props['mysql'])) continue;
                    $fldsql[$field] = $props['mysql'];
                }
                return $fldsql;
            break;
        }
        throw new \Exception("Not implemented transformations to FldSql from ".$this->getType());
    }

    /**
     * return [
     * 'tablename' => [
     *    'field1' => 'longtext not null',
     *    'field2' => 'longtext not null',
     *  ]
     * ]
     */      
    public function getTabFldSql():array {
        throw new \Exception("Not implemented transformations to TabFldSql from ".$this->getType());
        switch($this->getType()) {
            case '[Tab->Fld->Sql]':
            
            break;
            case '[Tab->Fld->Prp]':
            
            break;
            case '[Mod->???]':
                throw new \Exception("Not implemented transformation on [Mod->???]");
            break;
            case '[Fld->Sql]':
            
            break;
            case '[Fld->Prp]':
            
            break;
        }
    }

    /**
     *             
     * can return [Tab->Fld->Sql]
     * can return [Tab->Fld->Prp]
     * can return [Mod->???]
     * can return [Fld->Sql]
     * can return [Fld->Prp]
     */
    public function getType():string {
        return (new Modelv2($this->model))->getModelType();
    }
}