<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Legacy\SmartReplace as fn1;

trait QueryCommon {    
    /**
     * Informe quantos nomes de tabela desejar usar, com ou sem prefixo.
     * A função irá colocar todos os nomes das tabelas e uma abreviação para chamá-las mais facilmente no resto do escopo.
     * Por padrão, a abreviação é simplesmente {tA}, {tB}, {tC} e assim por diante.
     * Para invocar um nome de abreviação personalizado, use nomeTabela|suaAbreviação como parâmetro
     * A primeira dessas tabelas precisa ser a principal, ou seja, a que será utilizada em FROM.
     * @param string nomeTabela
     * @param string nomeTabela|minhaAbreviação
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
        $this->fields[fn1::smart_replace($campo, $this->tables, true)] = fn1::smart_replace($query, $this->tables, true);
        return $this;
    }
    

    /**
     * Alias para um setField simples, ou seja, sem subquerys. Por isso, é possível passar múltiplos campos no parâmetro
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
     * Use essa função para limpar alguns atributos. Útil para usar um SELECT COUNT em que você só deseja manter os Wheres
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
        $this->from = fn1::smart_replace($tabela, $this->tables, true);
        return $this;
    }

    public function leftJoin(string $tabela, string $where) {
        $this->from.= fn1::smart_replace("\r\n\tLEFT JOIN $tabela \r\n\tON \r\n\t$where", $this->tables, true);
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
}