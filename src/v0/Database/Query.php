<?php

/**
 * 180712 - Mais Completo
 * 190712 - Adicionado multiplos setOrders e possibilidade de editar a tabela em FROM..
 * 190509 - Sync para garantir que essa � a �ltima vers�o (09/05/2019)
 * 190605 - Adicionado Sistema de Logs. Padr�o de grava��o em log/cache/ checar exist�ncia dessa p�gina antes de usar essa classe
 */

namespace RBFrameworks\Database;

class Query {
    
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
    
    private $logfolder = "log/cache/";
    public $name = "query"; //nome para fins de log
    
    public function __construct() {
        global $RBVars;
        $this->prefixo = (!isset($RBVars['database']['prefixo'])) ? $GLOBALS['prefixo'] : $RBVars['database']['prefixo'];
        $this->alfabeto = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $this;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function writeLog($message) {
        
        //Defini��o do Nome do Arquivo
        $id = $this->name;
        $filename = $this->logfolder.'QueryLogs_'. $id.'.sql';
        
        //Tratamento com a Mensagem do Log
        $message = str_replace("\r\n", " ", $message);
        $message = str_replace("\t", " ", $message);
        $message = str_replace("  ", " ", $message);
        $message = str_replace("  ", " ", $message);
        
        $prefix = "/* ".date("Y-m-d H:i:s")." [".$_SERVER['REMOTE_ADDR']."] in ".__FILE__." */ \n";

        file_put_contents($filename, $prefix.$message."\r\n\r\n", FILE_APPEND);
    }
    
    public function setGroup($campo) {
        $this->groupby = smart_replace($campo, $this->tables, true);
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
    
    public function setFieldsFromArray(array $fields):object {
        foreach($fields as $field => $label) {
            if(is_string($field) and is_string($label)) {
                $this->setField($field, $label);
            } else {
                $this->setField($label);
            }
        }
        return $this;
    }
    
    /**
     * Use essa fun��o para limpar alguns atributos. �til para usar um SELECT COUNT em que voc� s� deseja manter os Wheres
     */
    public function clear(array $manter = []) {
        $manter = array_flip($manter);
        if(!isset($manter['fields'])) {
            $this->fields = array();
        }
        if(!isset($manter['where'])) {
            $this->clauses = [];
        }
        if(!isset($manter['limit'])) {
            $this->limit_min = 0;
            $this->limit_max = 0;
            $this->limit = null;
        }
        if(!isset($manter['order'])) {
            $this->order = null;
        }
        return $this;
    }
    
    public function setFrom(string $tabela) {
        $this->from = smart_replace($tabela, $this->tables, true);
        return $this;
    }

    public function leftJoin(string $tabela, string $where) {
        $this->from.= smart_replace("\r\n\tLEFT JOIN $tabela \r\n\tON \r\n\t$where", $this->tables, true);
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
    
    public function setWhereFree($condicao) {
        $this->clauses[] = smart_replace(" $condicao ", $this->tables, true);
        return $this;
    }
    
    /**
     * Executa a cl�usula do Where mediante $expression ser true
     * Seria equivalente a usar, por exemplo:
     * if($expression == true) $minhaQuery->setWhere($condicao, $param2, $param3);
     * O m�todo setWhereBool existe para manter o aspecto chainable de classe Query
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
            
        $this->clauses[] = smart_replace($condicao, $this->tables, true);
        return $this;
    }
    
    /**
     * Informa uma clausula WHERE campo IN (values)
     * @create 2020-02-06
     * @param string $field que ser� um campo para fazer a consulta com o $seach no banco de dados
     * @param array $search que ser� um array com os dados da consulta against o campo $field
     * @return object $this
     */
    public function setWhereIn(string $field, array $search): object {
        $values = "'".implode("','", $search)."'";
        $condicao = "`$field` IN ($values)";
        $this->clauses[] = smart_replace($condicao, $this->tables, true);
        return $this;
    }
    
    public function setGroupBy($string) {
        $this->groupby = smart_replace($string, $this->tables, true);
        return $this;
    }
    
    public function setOrder($field, $order = "ASC", $ignoreBracket = false) {
        
        if(strtoupper($field) == 'RAND()') {
            $this->order = $field;
            return $this;
        }
        
        if(!$ignoreBracket) {
            $field = str_replace('`', '', $field);
            $this->order.= smart_replace("`$field` $order, ", $this->tables, true);
        } else {
            $this->order.= smart_replace("$field $order, ", $this->tables, true);
        }
        return $this;
    }

    public function setOrderCastAsInt($field, $order = "ASC") {
        $field = 'CAST(`'.$field.'` AS SIGNED)';
        $this->setOrder($field, $order, true);
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
                return self::render_select();
            break;
            case 'UPDATE':
                return self::render_update();
            break;
            case 'INSERT':
                return self::render_insert();
            break;
            case 'DELETE':
                return self::render_delete();
            break;
            default:
                return self::render_select();
            break;
        }
    }
    private function render_select() {
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
        $this->writeLog($q);
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

    /**
     * Essa ideia foi tirada de https://stackoverflow.com/questions/28295756/replace-into-without-checking-auto-increment-primary-key 
     * Por�m, ainda n�o est� implementada, e talvez o local da Query nem esteja no local certo.
     * @return string
     */
    public function get_upsert():string {
        
        $tabela = reset($this->tables);
        
        return "INSERT INTO ".$tabela.' '.
    '('.parent::walk_query(['cod_produto'   => $cod_produto, 'qtd' => $estoque_in_erp['disponivel']], 'campos').')'.
    ' VALUES ('.$EstoqueEcom->walk_query(['cod_produto'   => $cod_produto, 'qtd' => $estoque_in_erp['disponivel']], 'values').')'.
    ' ON DUPLICATE KEY UPDATE '.
    parent::walk_query(['cod_produto'   => $cod_produto], 'update_values');
    }

    
}