<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Debug;

trait TableOperations {

    private $is_temporary_table = false;
    public function isTemporary():object {
        $this->is_temporary_table = true;
        return $this;
    }

    public function table_exists():bool {
        $res = $this->query("SHOW TABLES LIKE '{$this->getTabela()}'");
        return count($res) ? true : false;
    }

	public function field_exists(string $campo):bool {
        $res = $this->query("SHOW COLUMNS FROM `{$this->getTabela()}` WHERE Field = '$campo'");
        return count($res) ? true : false;
	}

    public function exists(string $key, $value = null):bool {
        $num = $this->queryFirstField("SELECT COUNT(`{$this->primary}`) FROM `{$this->getTabela()}` WHERE `$key` = '$value' LIMIT 1");
        return ($num > 0) ? true : false;
    }

    public function drop_table():bool {
        if($this->table_exists()) {
            $res = $this->query("DROP TABLE `{$this->getTabela()}`");
            return $this->table_exists();
        }
        return false;
    }

    public function build():object {
        if($this->table_exists()) {
            $this->alterTable();
        } else {
            $this->is_temporary_table ? $this->createTemporaryTable() : $this->createTable();
        }
        return $this;
    }

    public function createTemporaryTable() {
        $this->drop_table();
        //CreateTableStatement as TemporaryTable
        $query = $this->getQueryOperation_CreateTable('MEMORY');
        Debug::log($query, [], 'database.build');
        $this->query($query);
    }

    public function createTable() {
        //CreateTableStatement
        $query = $this->getQueryOperation_CreateTable('INNODB');
        Debug::log($query, [], 'database.build');
        $this->query($query);
    }

    public function alterTable() {
        //VarrerModel para AddCamposInextientes
        foreach($this->getQueryOperation_AlterTable() as $query) {
            if(!empty($query)) {
                Debug::log($query, [], 'database.build');
                $this->query($query);
            }
        }
    }
    
    public function getDatecreateTable():int {
        $date = $this->queryFirstField("SELECT CREATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$this->getTabela()}'");
        return strtotime($date);
    }

    public function getDateupTable():int {
        $date = $this->queryFirstField("SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$this->getTabela()}'");
        return strtotime($date);
    }

    //Retorna o próximo valor do AUTO_INCREMENT
    public function getNextAutoIncrement() {
        return $this->queryFirstField("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$this->getTabela()}'");
    }    

    public function getValue(string $field, int $primary, string $primary_name = null) {
        if(is_null($primary_name)) $primary_name = $this->getModelObject()->primary;
        if(empty($primary_name)) throw new \Exception("Ocorreu um erro ao validar a chave $primary para $field");
        return $this->queryFirstField("SELECT `{$field}` FROM `{$this->getTabela()}` WHERE `{$primary_name}` = $primary LIMIT 1");
    }

    public function generateModelFromTable():array {
        $model = [];
        $p = $this->query("DESCRIBE `{$this->getTabela()}`");
        foreach($p as $i => $column) {
            $column_name = $column['Field']; //=> coluna_a
            $column_type = $column['Type']; //=> int(10) unsigned
            $column_null = $column['Null'] == 'NO' ? 'NOT NULL' : '';
            $column_special = $column['Extra'].' ';
            if($column['Key'] == 'PRI') $column_special.= 'PRIMARY';
            if($column['Key'] == 'UNI') $column_special.= 'UNIQUE';
            $model[$column_name] = [
                'mysql' => $column_type.' '.$column_null.' '.$column_special,
                'mysql_default' => $column['Default'],
            ];
        }
        return $model;
    }
    
}