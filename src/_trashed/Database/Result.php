<?php

namespace RBFrameworks\Database;

trait Result {
    
    public function getArray():array {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getColumn(int $number = 0):array {
        return $this->statement->fetchAll(\PDO::FETCH_COLUMN, $number);
    }
    
    public function getColumnAndGroup():array {
        return $this->statement->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
    }
    
    /*
    public function getNextAutoIncrement() {
        $var = $db->query("SHOW TABLE STATUS LIKE 'my_table'")->fetch(PDO::FETCH_ASSOC)['Auto_increment'];
    }
    */
    
}
