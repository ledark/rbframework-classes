<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Legacy\SmartReplace as fn1;

trait QueryWhere {

    public function setGroup($campo) {
        $this->groupby = fn1::smart_replace($campo, $this->tables, true);
        return $this;
    }
    /**
     * As funções $Query->setWhereAnd e $Query->setWhereOr 
     * são exatamente iguais a $Query->setWhere, execeto por colorarem o sufixo
     * AND e OR respectivamente.
     */
    public function setWhereAnd($p1, $p2 = null, $p3 = null) {
        $this->setWhere($p1, $p2, $p3);
        $condicao = array_pop($this->clauses);
        $this->clauses[] = " ($condicao) AND ";
        return $this;
    }
    public function setWhereOr($p1, $p2 = null, $p3 = null) {
        $this->setWhere($p1, $p2, $p3);
        $condicao = array_pop($this->clauses);
        $this->clauses[] = " ($condicao) OR ";
        return $this;
    }
    
    public function setWhereFree($condicao) {
        $this->clauses[] = fn1::smart_replace(" $condicao ", $this->tables, true);
        return $this;
    }
    
    /**
     * Executa a cláusula do Where mediante $expression ser true
     * Seria equivalente a usar, por exemplo:
     * if($expression == true) $minhaQuery->setWhere($condicao, $param2, $param3);
     * O método setWhereBool existe para manter o aspecto chainable de classe Query
     */
    public function setWhereBool($expression, $condicao, $param2 = null, $param3 = null) {
        if($expression) $this->setWhere ($condicao, $param2, $param3);
        return $this;
    }
    public function setWhereBoolAnd($expression, $condicao, $param2 = null, $param3 = null) {
        if($expression) $this->setWhereAnd ($condicao, $param2, $param3);
        return $this;
    }    
    public function setWhereBoolOr($expression, $condicao, $param2 = null, $param3 = null) {
        if($expression) $this->setWhereOr ($condicao, $param2, $param3);
        return $this;
    }    
    
     /**
     * 
     * $Query->setWhere("status = 2"); //Trazer os campos where status = 2.
     * $Query->setWhere("status", 2); //Trazer os campos where status = 2.
     */
    public function setWhere($condicao, $param2 = null, $param3 = null) {
        
        if(is_string($condicao) and is_array($param2) and is_null($param3)) {
            return $this->setWhereIn($condicao, $param2);
        }
        
        if(is_string($condicao) and !is_null($param2)) {
            if(is_null($param3) and is_numeric($param2)) $param3 = "INT";
            switch($param3) {
                case 'INT':
                    $condicao = "`$condicao` = $param2";
                break;
                case 'LIKES':
                    $condicao = "`$condicao` LIKE '$param2'";
                break;
                case 'LIKE':
                    $condicao = "`$condicao` LIKE '%$param2%'";
                break;
                case 'NOT LIKE':
                    $condicao = "`$condicao` NOT LIKE '%$param2%'";
                break;
                case '>':
                    $condicao = "$condicao > $param2";
                break;
                default:
                    $condicao = "`$condicao` = '$param2'";
                break;
            }
        }
            
        $this->clauses[] = fn1::smart_replace($condicao, $this->tables, true);
        return $this;
    }
    
    /**
     * Informa uma clausula WHERE campo IN (values)
     * @create 2020-02-06
     * @param string $field que será um campo para fazer a consulta com o $seach no banco de dados
     * @param array $search que será um array com os dados da consulta against o campo $field
     * @return object $this
     */
    public function setWhereIn(string $field, array $search): object {
        $values = "'".implode("','", $search)."'";
        $condicao = "`$field` IN ($values)";
        $this->clauses[] = fn1::smart_replace($condicao, $this->tables, true);
        return $this;
    }
    
    public function setGroupBy($string) {
        $this->groupby = fn1::smart_replace($string, $this->tables, true);
        return $this;
    }
    
    public function setOrder($field, $order = "ASC", $ignoreBracket = false) {
        
        if(strtoupper($field) == 'RAND()') {
            $this->order = $field;
            return $this;
        }
        
        if(!$ignoreBracket) {
            $field = str_replace('`', '', $field);
            $this->order.= fn1::smart_replace("`$field` $order, ", $this->tables, true);
        } else {
            $this->order.= fn1::smart_replace("$field $order, ", $this->tables, true);
        }
        return $this;
    }

    public function getWhere(array $ignores = ['groupby', 'order', 'limit']) {
        $q = "";
        if(count($this->clauses)) {
            foreach($this->clauses as $clause) {
                $q.= " $clause \r\n";
            }
        }
        $q = rtrim($q, " AND \r\n")."\r\n";
        $q = rtrim($q, " OR \r\n")."\r\n";
        $q = rtrim($q, "AND ");
        $q = rtrim($q, "OR ");
        if(!is_null($this->groupby) and !isset($ignores['groupby'])) {
            $q.= "GROUP BY ".$this->groupby." \r\n";
        }        
        if(!is_null($this->order) and !isset($ignores['order'])) {
            $q.= "ORDER BY ".$this->order." ";
        }
        if(!is_null($this->limit) and !isset($ignores['limit'])) {
            $q.= "LIMIT ".$this->limit. " ";
        }
        $q = rtrim($q, ", ");
        $q = rtrim($q, " AND \r\n")."\r\n";
        $q = rtrim($q, " OR \r\n")."\r\n";
        return $q;
    }    

}