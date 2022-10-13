<?php

namespace RBFrameworks\Database;

trait Result {
    
    public function getArray():array {
        if($this->preventExecute) return [];
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getColumn(int $number = 0):array {
        if($this->preventExecute) return [];
        return $this->statement->fetchAll(\PDO::FETCH_COLUMN, $number);
    }
    
    public function hasRows(): bool {
        if($this->preventExecute) return false;
        $this->statement->fetch(\PDO::FETCH_COLUMN, 0);
        return ($this->statement->rowCount() > 0) ? true : false;
    }
    
    public function rowCount():int {
        if($this->preventExecute) return false;
        $rs1 = $this->query('SELECT FOUND_ROWS()');
        $this->statement->fetch(\PDO::FETCH_COLUMN, 0);
        return $this->statement->rowCount();
    }
    
    public function getColumnAndGroup():array {
        if($this->preventExecute) return [];
        return $this->statement->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
    }
    
    /*
    public function getNextAutoIncrement() {
        $var = $db->query("SHOW TABLE STATUS LIKE 'my_table'")->fetch(PDO::FETCH_ASSOC)['Auto_increment'];
    }
    */
    
}
