<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Types\PropProps;

trait TableQueryOperations {

    private function getQueryOperation_addField(string $field, array $props):string {

            if($this->field_exists($field)) return "";
            $query = "ALTER TABLE `{$this->getTabela()}` ADD `{$field}` {$props['mysql']} ";
            $previousField = $this->getModelObject()->getPreviousField($field);
            $query.= ($previousField === false) ? "FIRST" : "AFTER `$previousField`";
            
            if(!$this->field_exists($previousField)) $query = str_replace("AFTER `$previousField`", '', $query);
            return $query.';';
                
    }

    private function getQueryOperation_AlterTable():array {
        $queryArray = [];
        //$query = "";
        $model = $this->getModelObject();
        foreach($model->model as $field => $props) {
            //$query.= $this->getQueryOperation_addField($field, $props);
            $queryArray[] = $this->getQueryOperation_addField($field, $props);
        }
        //return $query;
        return $queryArray;
    }

    public function getQueryOperation_CreateTable(string $engine = 'INNODB'):string {
        $index = null;
        $unique = null;
        $key = null;
        
        if($engine == 'MEMORY') {
            $this->drop_table();
            $query = "CREATE TEMPORARY TABLE `{$this->tabela}` (";
        } else {
            $query = "CREATE TABLE IF NOT EXISTS `{$this->tabela}` (";
        }

        $model = $this->getModelObject()->model;
        $modelOriginal = $model;
        //expected $model = ['table' => ['field' => 'value', 'field2' => 'value2']]
        $hasString = 0;
        foreach(array_keys($model) as $key => $value) {
            if(is_string($key)) $hasString++;
        }

        if($hasString == 0) {
            $model = reset($model);
            $model = reset($model);
        }

        if($model instanceof PropProps) {
            $model = $model->getValue();
        }
        if(!is_array($model)) {
            $model = reset($modelOriginal);
        }
        
        foreach($model as $field => $props) { 

            
            //ResolveSyntaxe
            $sintaxe = trim($props['mysql']);
			$sintaxe = rtrim($sintaxe, ',');
            if(substr($sintaxe, 0, 4) == 'null') continue;
			if(strpos($sintaxe, ' INDEX') !== false) { $index = $field; $sintaxe = rtrim($sintaxe, ' INDEX');}
			if(strpos($sintaxe, ' ADDKEY') !== false) { $key = $field; $sintaxe = rtrim($sintaxe, ' ADDKEY');}
			if(strpos($sintaxe, ' PRIMARY') !== false) { 
                $sintaxe = str_replace(' PRIMARY', '', $sintaxe); 
                $primary = "$field";
            }
            
            //Query Final
            $query.= "`{$field}` {$sintaxe}, ";
        }
        
        if(isset($this->modelObject->uncaught['PRIMARY'])) {
            $query.= " PRIMARY KEY (`{$this->modelObject->uncaught['PRIMARY']}`)";
        } else 
        if(isset($primary)) {
            $query.= " PRIMARY KEY (`{$primary}`)";
        } else {
            $query = rtrim($query, ', ');
        }
        
        if(is_null($index) and is_null($unique) and is_null($key)) $sintaxe = rtrim($sintaxe, ',');
        
        //Index and Key
		if(!is_null($index)) {
			$index = trim($index);
			$index = (substr($index, 0, 1) != '`') ? '`'.$index.'`' : $index;
			$index = 'INDEX ( '.$index.' ) ,';
        } else {
            $index = '';
        }
		if(!is_null($key) and is_string($key)) {
			$key = trim($key);
			$key = (substr($key, 0, 1) != '`') ? '`'.$key.'`' : $key;
			$key = 'KEY ( '.$key.' ) ';
		} else {
			$index = rtrim($index, ',');
            $key = '';
		}	        
        
        $query = trim($query);
        $query = rtrim($query, ',');
        $query.= " {$index} {$key}  ";
        $query.= ' ) ENGINE =  '.$engine;

        echo $query;

       // $query.= ($tipo == 'temp') ? ' ) ENGINE = MEMORY ' : ' ) ENGINE =  '.$engine;
        return $query;
    }
    
    private function getQueryOperation_addCollumn(string $field, string $sql, string $afterField = null):string {
        if(is_numeric($field)) return "";
        $q = "ALTER TABLE `{$this->getTabela()}` ADD `{$field}` {$sql} ";
        $q.= is_null($afterField) ? "FIRST;" : "AFTER `$afterField`;";
        return $q;
    }


}