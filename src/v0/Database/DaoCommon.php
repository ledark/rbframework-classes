<?php

namespace RBFrameworks\Database;

trait DaoCommon {
    
    public function walk_query(array $dados, string $return = '?') {
        $str = "";
        foreach($dados as $campo => $valor){
            if( !array_key_exists($campo, $this->Model->model) ) continue;
            if(isset($this->Model->model[$campo]['format'])) $this->Model->model[$campo]['format']($valor, $campo, $dados);
            switch($return) {
                case 'campos': 
                    $str.= "`$campo`, ";
                break;
                case '?': 
                    $str.= "?, ";
                break;
                case 'values': 
                    $str.= "'$valor', ";
                break;
                case 'array_values': 
                    $arr[] = $valor;
                break;
                case 'update':
                    $str.= "`$campo` = ?, ";
                break;
                case 'update_values':
                    $valor = self::sanitize($valor);
                    $str.= "`$campo` = '$valor', ";
                break;
                case 'upsert':
                    $str.= "`$campo` = VALUES('$valor'), ";
                break;
                case 'where_and':
                    $valor = self::sanitize($valor);
                    $str.= "`$campo` = '$valor' AND ";
                break;
                case 'where_or':
                    $valor = self::sanitize($valor);
                    $str.= "`$campo` = '$valor' OR ";
                break;
            }
        }
        switch($return) {
            case 'array_values':
                return $arr;
            break;
            default:
                $str = rtrim($str, ", ");
                $str = rtrim($str, "AND ");
                $str = rtrim($str, "OR ");
                return $str;
            break;
        }
    }
        
    public function add(array $dados) {        
        $fields = $this->walk_query($dados, 'campos');
        $marks = $this->walk_query($dados, '?');
        $values = $this->walk_query($dados, 'array_values');
        if($this->Database->setQuery("INSERT INTO `$this->tabela` ({$fields}) VALUES ({$marks})")->setValues($values)->execute()->hasRows()) {
            return $this->Database->setQuery("SELECT LAST_INSERT_ID()")->execute()->getColumn()[0];
        } else {
            if(!$this->Database->preventExecute()) {
                $marks = $this->walk_query($dados, 'values');
                Utils\LoggerStatic::add("INSERT INTO `$this->tabela` ({$fields}) VALUES ({$marks})", 'sql.addError', $_SESSION);
            }
            return null;
    }
    }
    
    //@todo
    public function set(array $dados, int $primary) {
        $fields = $this->walk_query($dados, 'update');
        $values = $this->walk_query($dados, 'array_values');
        $primaryfield = $this->Model->primary;
        $query = "UPDATE `$this->tabela` SET {$fields} WHERE `$primaryfield` = $primary LIMIT 1";
        if($this->Database->setQuery($query)->setValues($values)->execute()->hasRows()) {
            
        }
    }
    
    //@todo
    public function get() {
        
    }
    
    //@todo
    public function del() {
        
    }
    
    //@todo
    public function insert() {
        
    }
    
    //@todo
    public function update(array $dados, array $keys) {
        $table          = $this->tabela;
        $keys_where     = $this->walk_query($keys, 'where_and');
        $update_values  = $this->walk_query($dados, 'update_values');
        $campos         = $this->walk_query($dados, 'campos');
        $values         = $this->walk_query($dados, 'values');
        
        $query = "UPDATE `$table` SET $update_values WHERE $keys_where";

        return $this->Database->setQuery($query)->execute()->rowCount();        
    }
    
    public function upsert(array $dados, array $keys) {
        $table          = $this->tabela;
        $keys_where     = $this->walk_query($keys, 'where_and');
        $update_values  = $this->walk_query($dados, 'update_values');
        $campos         = $this->walk_query($dados, 'campos');
        $values         = $this->walk_query($dados, 'values');
        
        $query = "UPDATE `$table` SET $update_values WHERE $keys_where; INSERT INTO `$table` ($campos) SELECT $values FROM `$table` WHERE $keys_where HAVING COUNT(*) = 0 ";

        return $this->Database->setQuery($query)->execute()->rowCount();
    }


    public function replace(array $dados, array $keys = [], $limit = null) {
        
        //Extraction
        $fields = $this->walk_query($dados, 'campos');
        $marks = $this->walk_query($dados, '?');
        $values = $this->walk_query($dados, 'array_values');
        $limit = self::toMysql_limit($limit);
        
        if(count($keys)) {
            
            $keysFields = $this->walk_query($keys, 'campos');
            $keysWheres  = $this->walk_query($keys, 'where_and');
            $existingValues = $this->Database->setQuery("SELECT {$keysFields} FROM `$this->tabela` WHERE {$keysWheres} $limit")->execute()->hasRows();
            $update = $this->walk_query($dados, 'update');
            
            if($existingValues) {
                $query = "UPDATE `$this->tabela` SET {$update} WHERE {$keysWheres} $limit";
            } else {
                $query = "INSERT INTO `$this->tabela` ({$fields}) VALUES ({$marks})";
            }
            
        } else {
            $query = "REPLACE INTO `$this->tabela` ({$fields}) VALUES ({$marks})";
        }
       
       
        if($this->Database->setQuery($query)->setValues($values)->execute()->hasRows()) {
            return true;
        } else {
            if(!$this->Database->preventExecute()) {
                $marks = $this->walk_query($dados, 'values');
                Utils\LoggerStatic::add($query, 'sql.addError', $_SESSION);
            }
            return null;
        }
    }
    
    public function delete(array $keys) {
        $table          = $this->tabela;
        $keys_where     = $this->walk_query($keys, 'where_and');        
        return $this->Database->setQuery("DELETE FROM `$table` WHERE $keys_where")->execute()->rowCount();        
    }
    
    /**
     * Como forma de implementação, pode ser passado para o select um objeto do tipo QUERY
     * @example 
     *  select('campos', [filtros])
     * @param type $param1 pode ser:
     * @return type
     */
    public function select($mixed = null, array $filters = [], string $sufix = '') {
        switch(strtolower(gettype($mixed))) {
            case 'null':
                $query = $this->getRegistredQuery('all');
            break;
            case 'string':
                if($this->isRegistredQuery($mixed)) {
                    $query = $this->getRegistredQuery($mixed);
                } else {
                    $query = $mixed;
                }
            break;
            case 'array':
                if(count($filters)) {
                    $query = "SELECT ".implode(',', $mixed)." FROM `$this->tabela` WHERE ".$this->walk_query($filters, 'where_and').' '.$sufix;
                } else {
                    $query = "SELECT * FROM `$this->tabela` WHERE ".$this->walk_query($mixed, 'where_and').' '.$sufix;
                }
            break;
        }
        $dados = $this->Database->setQuery($query)->execute()->getArray();        
        $dados = $this->Model->humanize($dados);
        return $dados;
    }
    
    public function farray(string $query):array {
        return $this->select($query);
    }
    
    /*
    private function array2whereClauses(array $filters):string {
        if(!count($filters)) return '';
        $where = "WHERE ";
        foreach($filters as $chave => $valor) {
            $chave = "`{$chave}`";
            $operator = " = ";
            $valor = "'$valor'";
            $where.= "{$chave}{$operator}{$valor}";
        }
        return $where;
    }
    */ 

    private static function sanitize($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    } 
    
    /**
     * Converte um valor qualquer para a Sintax "limit" do Mysql
     * @param NULL|INT|ARRAY $value
     * @return string
     */
    public static function toMysql_limit($value):string {
        if(is_numeric($value) or is_int($value) ) {
            return " LIMIT $value ";
        } else
        if(is_array($value)) {
            return " LIMIT ".$value[0].','.$value[1];
        }
        return '';
    }
    
    public function getValue(string $name, array $filter = []):string {
        $query = "SELECT `$name` FROM `$this->tabela` WHERE ".$this->walk_query($filter, 'where_and');
        $return = $this->select($query)[0][$name];
        return is_string($return) ? $return : $query;
    }    
    
}
