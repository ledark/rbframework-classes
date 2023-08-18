<?php

namespace RBFrameworks\Storage\Database;

trait DaoTable {

    /**
     * Funções para Construção de Tabelas
     */
    //Build para Tabelas
    private function tableExists(string $tablename = ''): bool {
        if(!isset($tablename)) $tablename = $this->tabela;
        return $this->queryFirstField("SHOW TABLES LIKE '{$this->getTabela()}'") != null ? true : false;
    }    
    
    private function dropTable() {
        return $this->query("DROP TABLE `{$this->getTabela()}`");
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

    private static function sanitize($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    } 
    
    /**
     * Converte um valor qualquer para a Sintax "limit" do Mysql
     * @param NULL|INT|ARRAY $value
     * @return string
     */
    public static function toMysql_limit($value):string {
        if(is_numeric($value) or is_int($value) ) {
            return " LIMIT $value ";
        } else
        if(is_array($value)) {
            return " LIMIT ".$value[0].','.$value[1];
        }
        return '';
    }
    
    public function getValue(string $name, array $filter = []):string {
        $query = "SELECT `$name` FROM `$this->tabela` WHERE ".$this->walk_query($filter, 'where_and');
        $return = $this->select($query)[0][$name];
        return is_string($return) ? $return : $query;
    }        
    
}
