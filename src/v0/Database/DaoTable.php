<?php

namespace RBFrameworks\Database;

trait DaoTable {

    /**
     * Funções para Construção de Tabelas
     */
    private function tableExists(string $tablename = ''): bool {
        if(!isset($tablename)) $tablename = $this->tabela;
        return $this->Database->setQuery("SHOW TABLES LIKE '{$this->tabela}'")->execute()->hasRows();
    }
    
    private function dropTable() {
        return $this->Database->setQuery("DROP TABLE `{$this->tabela}`")->execute();
    }
   
    private function createTable(string $tipo = 'normal') {
        $index = null;
        $unique = null;
        $key = null;
        
        if($tipo == 'temp') {
            $this->dropTable();
            $query = "CREATE TEMPORARY TABLE `{$this->tabela}` (";
        } else {
            $query = "CREATE TABLE IF NOT EXISTS `{$this->tabela}` (\r\n";
        }
        
        foreach($this->Model->model as $field => $props) {
            
            
            
            //ResolveSyntaxe
            $sintaxe = trim($props['mysql']);
			$sintaxe = rtrim($sintaxe, ',');
            if(substr($sintaxe, 0, 4) == 'null') continue;
			if(strpos($sintaxe, ' INDEX') !== false) { $index = $campo; $sintaxe = rtrim($sintaxe, ' INDEX');}
			if(strpos($sintaxe, ' ADDKEY') !== false) { $key = $campo; $sintaxe = rtrim($sintaxe, ' ADDKEY');}
            
            //Query Final
            $query.= "\t`{$field}` {$sintaxe},\r\n";
        }
        
        if(isset($this->Model->uncaught['PRIMARY'])) {
            $query.= "\tPRIMARY KEY (`{$this->Model->uncaught['PRIMARY']}`)";
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
		if(!is_null($key)) {
			$key = trim($key);
			$key = (substr($key, 0, 1) != '`') ? '`'.$key.'`' : $key;
			$key = 'KEY ( '.$key.' ) ';
		} else {
			$index = rtrim($index, ',');
            $key = '';
		}	        
        
        $query.= "\r\n{$index}\r\n{$key}\r\n ";

        $query.= ($tipo == 'temp') ? ' ) ENGINE = MEMORY ' : ' ) ENGINE = MYISAM ';

        $this->Database->setQuery($query)->execute();
        return $this->tableExists();
    }
    
 
    public function getDateupTable() {
        $date = $this->Database->setQuery("SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '{$this->Database->database}' AND TABLE_NAME = '{$this->tabela}'")->execute()->getColumn(0)[0];
        return strtotime($date);
    }
    
    private function fieldExists(string $field) {
        return $this->Database->setQuery("SHOW COLUMNS FROM `{$this->tabela}` WHERE Field = '$field'")->execute()->hasRows();
    }
    
    private function createField(string $field, array $props) {
        
        $query = "ALTER TABLE `{$this->tabela}` ADD `{$field}` {$props['mysql']} ";
        $previousField = $this->Model->getPreviousField($field);
        $query.= ($previousField === false) ? "FIRST" : "AFTER `$previousField`";
        
        if(!$this->fieldExists($previousField)) $query = str_replace("AFTER `$previousField`", '', $query);
        
        if($this->Database->setQuery($query)->execute()) {
            return true;
        } else {
            $this->Logger->addQueryError($query);
            return false;
        }
        
    }
    
    
}
