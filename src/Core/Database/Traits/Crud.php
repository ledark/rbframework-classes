<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Utils\Arrays;

trait Crud {

    private function extractValidFields(array $fields):array {
        $validFields = [];
        $modelFields = Arrays::extractKeysFromAssocArray( $this->getModelObject()->model );

        if(Arrays::is_assoc($fields)) {
            foreach($fields as $chave => $valor) {
                if(in_array($chave, $modelFields)) $validFields[$chave] = $valor;
            }
        } else {
            foreach($fields as $chave) {
                if(in_array($chave, $modelFields)) $validFields[] = $chave;
            }            
        }
        return $validFields;
    }

    private function convertArray_toQuery(array $dados):object {
        $dados = $this->extractValidFields($dados);

        $fields = Arrays::extractFields($dados);
        $bindedValues = Arrays::extractBindNamedParams($dados);
        $values = Arrays::extractValuesAsArray($dados);
        $valuesRaw = Arrays::extractValues($dados);
        return (object) [
            'query_fields' => $fields,
            'query_values' => $valuesRaw,
            'query_values_binded' => Arrays::extractBindParams($dados),
            'query_field_binded' => Arrays::extractBindNamedParams($dados),
            'query_update_binded' => Arrays::extractUpdateBinded($dados),
            'query_update_raw' => Arrays::extractUpdateRaw($dados),
            /*
            'query_insert' => "INSERT INTO `{$this->getTabela()}` ($fields) VALUES ($bindedValues)",
            'fields' => $fields,
            'bindedValues' => $bindedValues,
            'values' => $valuesRaw,
            'bindedFieldsValues' => Arrays::extractBindNamedParamsWithValues($dados),
            */
            'validFields' => $dados,
        ];
    }

    public function add(array $dados) {
        $this->insert($this->getTabela(), $this->extractValidFields($dados));
        if($this->affectedRows()) {
            return $this->insertId() > 0 ? $this->insertId() : true;
        }
    }
    
    public function set(array $dados, array $keys):bool {        
        $this->update($this->getTabela(), $this->extractValidFields($dados), Arrays::extractWhereAnd($this->extractValidFields($keys)));
        return ($this->affectedRows() > 0) ? true : false;
    }
    
    public function upsert(array $dados, array $keys):bool {
        $update = $this->set($dados, $keys);
        if(!$update) {
            $res = $this->add($dados);
            if($res > 0 or $res === true) return true;
        } else {
            return true;
        }
        return false;
    }
    
    public function get(array $dados = [], array $criterias = [], string $querySufix = ""):array {
        $dados = $this->extractValidFields($dados);
        $criterias = $this->extractValidFields($criterias);
        $fields = Arrays::extractFields($dados);
        $fields = empty($fields) ? "*" : $fields;
        $query = "SELECT ".$fields." FROM `{$this->getTabela()}` WHERE 1=1 AND ".Arrays::extractWhereAnd($criterias);
        $query = rtrim($query, "AND ");
        $query = rtrim($query, "OR ");
        $query = $query." {$querySufix}";
        Debug::log($query, $criterias, "CoreDatabaseCRUD", "CoreDatabase");
        $res = $this->query($query);
        return !is_array($res) ? [] : $res;
    }
    
    public function del(array $keys)    {
        $this->delete($this->getTabela(), Arrays::extractWhereAnd($this->extractValidFields($keys)));
        return ($this->affectedRows() > 0) ? true : false;
    }

}