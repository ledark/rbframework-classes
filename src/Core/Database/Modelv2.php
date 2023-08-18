<?php

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Utils\Arrays;

class Modelv2
{
    private $original_model;
    private $model_type;
    private $table_name;
    private $prefixo;
    private $modelSql;
    private $modelPrp;
    
    public function __construct(array $model) {
        $this->original_model = $model;
        $this->checkModelType();
        $this->checkConstructor();
    }

    /**
     * Tratar cada uma das possibilidades de Model Existentes, gerando dessa forma os demais campos nessa classe:* 
     */

    public $modelFieldsWithProps;

    private function checkModelType() {

        switch($this->getModelType()) {
            case '[Tab->Fld->Sql]':

            break;
            case '[Tab->Fld->Prp]':

            break;
            case '[Mod->???]':

            break;
            case '[Fld->Sql]':
                
            break;
            case '[Fld->Prp]':

            break;
            default:
                throw new \Exception($this->getModelType()." Model Type is not supported or invalid");
            break;
        }
    }

    private function checkConstructor() {

    }

    public function getPrefixo():string {
        if(isset($this->prefixo)) return $this->prefixo;
        $this->prefixo = Config::get('database.prefixo');
        return $this->prefixo;
    }
    
    public function getTableName():string {
        if(isset($this->table_name)) return $this->table_name;
        if(count($this->original_model) == 1) {
            $tabela = key($this->original_model);
            $this->table_name = !is_string($tabela) ? 'NO_TABLE_NAME' : $tabela;
        } else {
            $this->table_name = 'NO_TABLE_NAME';
        }
        if($this->table_name == 'NO_TABLE_NAME') return $this->table_name;
        if(substr($this->table_name, 0, strlen($this->getPrefixo())) == $this->getPrefixo()) return $this->table_name;
        $this->table_name = $this->getPrefixo().$this->table_name;
        return $this->table_name;
    }

    private function getModelTypes() {
        /* [Fld->Sql] */ $NoTable_NoProps = ['Field' => 'MYSQL_SINTAXE']; 
        /* [Tab->Fld->Sql] */ $HasTable_NoProps = ['Table' => ['Field' => 'MYSQL_SINTAXE']];
        /* [Fld->Prp] */ $NoTable_PropSimple = ['Field' => ['mysql' => 'MYSQL_SINTAXE']];
        /* [Tab->Fld->Prp] */ $HasTable_PropSimple = ['Table' => ['Field' => ['mysql' => 'MYSQL_SINTAXE']]];
    }

    /**
     * @returns
     * Tab->Fld->Sql
     * Tab->Fld->Prp
     * Mod->???
     * Fld->Sql
     * Fld->Prp
     */
    public function getModelType():string {
        if(isset($this->model_type)) return $this->model_type;
        $this->model_type = '[';

        //OneField
        if(count($this->original_model) == 1) {
            if( is_string( key($this->original_model) ) and is_string( $this->original_model[key($this->original_model)] )) {
                $this->model_type.= 'Tab->Fld->Sql';
                $this->modelFieldsWithProps = [key($this->original_model) => ['mysql' => $this->original_model[key($this->original_model)]]];
            } else 
            if( is_string( key($this->original_model) ) and is_array( $this->original_model[key($this->original_model)] )) {
                $arr = $this->original_model[key($this->original_model)];
                $arr = reset($arr);
                if(is_string($arr)) {
                    $this->model_type.= 'Tab->Fld->Sql';
                    $this->modelFieldsWithProps = [key($this->original_model) => ['mysql' => $arr]];
                } else {
                    $this->model_type.= 'Tab->Fld->Prp';
                    $this->modelFieldsWithProps = [key($this->original_model) => $arr];
                }
            } else {
                $this->model_type.= 'Mod->???';
                $this->modelFieldsWithProps = $this->original_model;
            }

        //MultipleFields    
        } else {
            $model = [];
            if( is_string($this->original_model[key($this->original_model)])) {
                $this->model_type.= 'Fld->Sql';
                foreach($this->original_model as $field => $sql) {
                    $model[$field] = ['mysql' => $sql];
                }
                //$this->modelFieldsWithProps = [key($this->original_model) => ['mysql' => $this->original_model[key($this->original_model)]]];
            } else {
                $this->model_type.= 'Fld->Prp';
                foreach($this->original_model as $field => $props) {
                    $model[$field] = $props;
                }
                //$this->modelFieldsWithProps = [key($this->original_model) => $this->original_model[key($this->original_model)]];
            }
            $this->modelFieldsWithProps = $model;
        }
        $this->model_type.= ']';
        return $this->model_type;
    }

    /**
     * [
     *  tablename => [
     *      'props'
     *  ]
     * ]
     *
     * @return array modelFieldsWithProps
     */

    public function getModel():array {
        return $this->modelFieldsWithProps;
    }

    //Return 
    public function getModelFldSql(bool $strict = false):array {
        $model = [];
        foreach($this->modelFieldsWithProps as $Fld => $Prp) {
            $Sql = isset($Prp['mysql']) ? $Prp['mysql'] : $Prp;
            if(!is_string($Sql)) {
                ob_start();
                print_r($Sql);
                $Sql = ob_get_clean();
            }
            $model[$Fld] = $Sql;
        }
        return $model;
    }    

    public function getSchema():array {
        return $this->modelFieldsWithProps[$this->getTableName()];
    }



}