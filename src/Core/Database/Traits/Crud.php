<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Utils\Arrays;
use RBFrameworks\Core\Utils\ArraysDatabase;

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

        $fields = ArraysDatabase::extractFields($dados);
        $bindedValues = ArraysDatabase::extractBindNamedParams($dados);
        $values = ArraysDatabase::extractValuesAsArray($dados);
        $valuesRaw = ArraysDatabase::extractValues($dados);
        return (object) [
            'query_fields' => $fields,
            'query_values' => $valuesRaw,
            'query_values_binded' => ArraysDatabase::extractBindParams($dados),
            'query_field_binded' => ArraysDatabase::extractBindNamedParams($dados),
            'query_update_binded' => ArraysDatabase::extractUpdateBinded($dados),
            'query_update_raw' => ArraysDatabase::extractUpdateRaw($dados),
            /*
            'query_insert' => "INSERT INTO `{$this->getTabela()}` ($fields) VALUES ($bindedValues)",
            'fields' => $fields,
            'bindedValues' => $bindedValues,
            'values' => $valuesRaw,
            'bindedFieldsValues' => ArraysDatabase::extractBindNamedParamsWithValues($dados),
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
        $this->update($this->getTabela(), $this->extractValidFields($dados), ArraysDatabase::extractWhereAnd($this->extractValidFields($keys)));
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
        $fields = ArraysDatabase::extractFields($dados);
        $fields = empty($fields) ? "*" : $fields;
        $query = "SELECT ".$fields." FROM `{$this->getTabela()}` WHERE 1=1 AND ".ArraysDatabase::extractWhereAnd($criterias);
        $query = rtrim($query, "AND ");
        $query = rtrim($query, "OR ");
        $query = $query." {$querySufix}";
        Debug::log($query, $criterias, "CoreDatabaseCRUD", "CoreDatabase");
        $res = $this->query($query);
        return !is_array($res) ? [] : $res;
    }
    
    public function del(array $keys)    {
        $this->delete($this->getTabela(), ArraysDatabase::extractWhereAnd($this->extractValidFields($keys)));
        return ($this->affectedRows() > 0) ? true : false;
    }

}