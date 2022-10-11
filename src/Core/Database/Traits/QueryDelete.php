<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QueryDelete {
    
    private function render_delete():string {
        $q = "DELETE FROM ".$this->from." WHERE\r\n";
        foreach($this->clauses as $clause) {
            $q.= " $clause \r\n";
        }
        $q = trim($q, " AND \r\n")."\r\n";
        $q = trim($q, " OR \r\n")."\r\n";
        if(!is_null($this->limit)) {
            $q.= "LIMIT ".$this->limit;
        }
        return $q;
    }

}