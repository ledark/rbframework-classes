<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QueryInsert {
    
    private function render_insert():string {
        return "INSERT INTO ".$this->from." (".implode(',', array_keys($this->fields)).") VALUES (".implode(',', array_values($this->fields)).")";
    }

}