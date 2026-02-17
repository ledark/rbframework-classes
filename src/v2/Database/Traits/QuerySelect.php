<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QuerySelect {    
    private function render_select():string {
        $q = "SELECT \r\n";
        foreach ($this->fields as $campo => $query) {
            if($campo == "*" or strpos($campo, '*') !== false) {
                $q.= "\t$campo\r\n,";
            } else
            //13/06/2019 Adicionado cases especiais para o Select
            if( substr($campo, 0, 8) == 'DISTINCT' or 
                substr($campo, 0, 8) == 'COUNT' or 
                substr($campo, 0, 8) == 'MAX' or 
                substr($campo, 0, 8) == 'MIN' or 
                substr($campo, 0, 8) == 'SUM') {
                $q.= "\t$campo\r\n,";
                
            } else 
            if($campo == $query) {
                $q.= "\t`$campo`\r\n,";
            } else {
                $q.= "\t$query AS `$campo`\r\n,";
            }
            
        }
        $q = rtrim($q, ",");
        $q.= "FROM\r\n\t".$this->from."\r\n";
        if(count($this->clauses)) {
            $q.= "WHERE\r\n\t";
            $q.= "(1=1) AND ";
            foreach($this->clauses as $clause) {
                $q.= " $clause \r\n";
            }
        }
        $q = trim($q, " AND \r\n")."\r\n";
        $q = trim($q, " OR \r\n")."\r\n";
        if(!is_null($this->groupby)) {
            $q.= "GROUP BY ".$this->groupby." \r\n";
        }        
        if(!is_null($this->order)) {
            $q.= "ORDER BY ".$this->order." ";
        }
        if(!is_null($this->limit)) {
            $q.= "LIMIT ".$this->limit. " ";
        }
        if($this->write_log) $this->writeLog($q);
        return $q;
    }
}