<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QueryUpdate {    
    private function render_update():string {
        $q = "UPDATE ".$this->from." SET\r\n";
        foreach ($this->fields as $campo => $query) {
            if($campo != $query) {
                $q.= "\t`$campo` = '$query'\r\n,";
            }
        }
        $q = rtrim($q, ",");
        if(count($this->clauses)) {
            $q.= "WHERE\r\n\t";
            foreach($this->clauses as $clause) {
                $q.= " $clause \r\n";
            }
        }
        $q = trim($q, " AND \r\n")."\r\n";
        $q = trim($q, " OR \r\n")."\r\n";
        if(!is_null($this->order)) {
            $q.= "ORDER BY ".$this->order;
        }
        if(!is_null($this->limit)) {
            $q.= "LIMIT ".$this->limit;
        }
        return $q;        
    }
}