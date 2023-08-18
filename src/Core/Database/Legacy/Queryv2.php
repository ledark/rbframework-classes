<?php

namespace RBFrameworks\Core\Database\Legacy;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Plugin;

/**
 * 180712 - Mais Completo
 * 190712 - Adicionado multiplos setOrders e possibilidade de editar a tabela em FROM..
 * 211020 - Improved prefixo from new RBFramewors v98
 */

Plugin::load('smart_replace');

class Queryv2 {
    
    public $prefixo;
    public $tables = array(); //Alias para os nomes das tabelas
    public $fields = array();
    public $clauses = array(); //Array de Condi��es para Execu��o da Query
    public $groupby = null;  
    public $from = null;
    
    public $limit_min = 0;
    public $limit_max = 0;
    private $limit = null;
    private $order = null;
    private $alfabeto;
    public $type = "SELECT";
    
    public function __construct() {
        $this->prefixo = $this->generatePrefixo();
        $this->alfabeto = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    }

    private function generatePrefixo():string {
        global $RBVars;
        if(isset($RBVars) and isset($RBVars['database']['prefixo'])) {
            if(!empty($RBVars['database']['prefixo'])) return $RBVars['database']['prefixo'];
        }
        if(isset($GLOBALS['prefixo']) and !empty($GLOBALS['prefixo'])) {
            return $GLOBALS['prefixo'];
        }
        return Config::get('database.prefixo');
    }
    
    public function setGroup($campo) {
        $this->groupby = $campo;
        return $this;
    }
    
    public function setPrefix($prefix = null) {
        if(!is_null($prefix)) {
            $this->prefixo = $prefix;
        }
        return $this;
    }    
    
    /**
     * Informe quantos nomes de tabela desejar usar, com ou sem prefixo.
     * A fun��o ir� colocar todos os nomes das tabelas e uma abrevia��o para cham�-las mais facilmente no resto do escopo.
     * Por padr�o, a abrevia��o � simplesmente {tA}, {tB}, {tC} e assim por diante.
     * Para invocar um nome de abrevia��o personalizado, use nomeTabela|suaAbrevia��o como par�metro
     * A primeira dessas tabelas precisa ser a principal, ou seja, a que ser� utilizada em FROM.
     * @param string nomeTabela
     * @param string nomeTabela|minhaAbrevia��o
     * @return $this
     */
    public function useTables() {
        $count = 0;
        $args = func_get_args();
        if(count($args)) {
            foreach($args as $arg) {
                if(strpos($arg, '|') !== false) {
                    $argx = explode('|', $arg);
                    $argname = $argx[1];
                    $arg = $argx[0];
                    unset($argx);
                } else {
                    $argname = "t".$this->alfabeto[$count];
                }
                $this->tables[$argname] = $this->prefixo.str_replace($this->prefixo, '', $arg);
                $count++;
            }
        }
        return $this;
    }
    
    public function setField($campo, $query = null) {
        $campo = trim($campo);
        if(is_null($query)){
            $query = $campo;
            if(strpos($query, ",") !== false) {
                $query = explode(",", $query);
                foreach($query as $queryr) {
                    $this->setField($queryr);
                }
                return $this;
            }
        }
        if(substr(strtoupper($query), 0, 7) == "SELECT ") $query = "($query)";
        $this->fields[smart_replace($campo, $this->tables, true)] = smart_replace($query, $this->tables, true);
        return $this;
    }
    

    /**
     * Alias para um setField simples, ou seja, sem subquerys. Por isso, � poss�vel passar m�ltiplos campos no par�metro
     * @example $Query->setFields("cod", "nome", "description");
     * @example $Query->setFields("cod, nome, description");
     * @return $this
     */
    public function setFields() {
        $args = func_get_args();
        if(count($args)) {
            foreach($args as $arg) {
                $this->setField($arg);
            }
        }
        return $this;
    }
    
    public function setFrom($tabela) {
        $this->from = smart_replace($tabela, $this->tables, true);
        return $this;
    }
    
    /**
     * As fun��es $Query->setWhereAnd e $Query->setWhereOr 
     * s�o exatamente iguais a $Query->setWhere, execeto por colorarem o sufixo
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
    
     /**
     * 
     * $Query->setWhere("status = 2"); //Trazer os campos where status = 2.
     * $Query->setWhere("status", 2); //Trazer os campos where status = 2.
     */
    public function setWhere($condicao, $param2 = null, $param3 = null) {
        if(is_string($condicao) and !is_null($param2)) {
            if(is_null($param3) and is_numeric($param2)) $param3 = "INT";
            switch($param3) {
                case 'INT':
                    $condicao = "`$condicao` = $param2";
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
            
        $this->clauses[] = smart_replace($condicao, $this->tables, true);
        return $this;
    }
    
    public function setGroupBy($string) {
        $this->groupby = $string;
        return $this;
    }
    
    public function setOrder($field, $order = "ASC", $ignoreBracket = false) {
        if(!$ignoreBracket) {
            $field = str_replace('`', '', $field);
            $this->order.= smart_replace("`$field` $order, ", $this->tables, true);
        } else {
            $this->order.= smart_replace("$field $order, ", $this->tables, true);
        }
        return $this;
    }
    
    public function setType($type = "SELECT") {
        $this->type = strtoupper($type);
        return $this;
    }
    
    public function setLimit($min, $max = null) {
        if(is_null($max)) {
            $this->limit_max = null;
            $this->limit = $min;
        } else {
            $this->limit_min = $min;
            $this->limit_max = $max;
            $this->limit = "$min,$max";
        }
        return $this;
    }

    /**
     * Renderiza o c�digo, utilizando as configura��es especificadas anteriormente.
     * @return string
     */
    public function render() {
        if(!is_null($this->order)) $this->order = rtrim($this->order, ', ');
        $this->from = (is_null($this->from)) ? "`".reset($this->tables)."`" : $this->from;
        switch ($this->type) {
            case 'SELECT':
                return $this->render_select();
            break;
            case 'UPDATE':
                return $this->render_update();
            break;
            case 'INSERT':
                return $this->render_insert();
            break;
            case 'DELETE':
                return $this->render_delete();
            break;
            default:
                return $this->render_select();
            break;
        }
    }
    private function render_select() {
        $q = "SELECT \r\n";
        foreach ($this->fields as $campo => $query) {
            if($campo == "*") {
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
        return $q;
    }
    private function render_update() {
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
    private function render_insert() {
        
    }
    private function render_delete() {
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