<?php

namespace RBFrameworks\Database\Utils;

class Pairs extends \RBFrameworks\Database\Dao {
    
    public function __construct($name = 'meta') {
        
        $Database = new \RBFrameworks\Database('doDBv3');
        $Model = new \RBFrameworks\Database\Model([$name => [
            'chave'     => ['mysql' => 'VARCHAR(255) NOT NULL PRIMARY UNIQUE'],
            'valor'     => ['mysql' => 'LONGTEXT NOT NULL'],
        ]]);
        
        parent::__construct($Database, $Model);
        
        $this->build();
    }
    
    public function setVarname(string $varname) {
        plugin('mysql_escape_mimic');
        $this->varname = mysql_escape_mimic($varname);
        return $this;
    }
    
    public function getStringFromVariable($mixed):string {
        switch(gettype($mixed)) {
            case "boolean":
                return ($mixed) ? 'bool:true' : 'bool:false';
            break;
            case "integer":
                return (string) $mixed;
            break;
            case "double":
                return (string) $mixed;
            break;
            case "string":
                return (substr($mixed, 4, 1) == ':') ? ' '.mysql_escape_mimic($mixed) : mysql_escape_mimic($mixed);
            break;
            case "array":
                return '_arr:'.serialize($mixed);
            break;
            case "object":
                $array = (array) $mixed;
                return 'data:'.base64_encode(serialize($array));
            break;
            case "resource":
                throw new Exception("Tipo de variável do tipo resource não pode ser salvo em banco de dados");
            break;
            case "NULL":
                return "null:";
            break;    
            default:
                return (string) $mixed;
            break;
        }
        throw new Exception("Tipo de variável não é possível de ser salvo no banco de dados");
    }
    
    private function isMixed($value) {
        return (substr($value, 4, 1) == ':') ? true : false;
    }
    
    public function getMixedFromString($value) {
        if($this->isMixed($value)) {
            switch(substr($value, 0, 4)) {
                case 'bool':
                    $value = substr($value, 5);
                    return ($value == 'true') ? true : false;
                break;
                case '_arr':
                    $value = substr($value, 5);
                    return unserialize($value);
                break;
                case 'data':
                    $value = substr($value, 5);
                    return unserialize(base64_decode($value));
                break;
                case 'null':
                    return null;
                break;
            }
        } 
        return $value;      
    }
    
    public function set($mixed) {
        $valor = $this->getStringFromVariable($mixed);

        if(empty($valor) or $valor == 'null:') {
            $this->Database->farray("DELETE FROM $this->tabela WHERE chave = '{$this->varname}' LIMIT 1");
        } else {
            $this->farray("REPLACE INTO $this->tabela SET chave = '{$this->varname}', valor = '$valor'");
        }
    }
    
    public function get() {
        $value = $this->farray("SELECT valor FROM $this->tabela WHERE `chave` = '{$this->varname}' LIMIT 1");
        if(count($value)) {
            return $this->getMixedFromString($value[0]['valor']);
        } else {
            return '';
        }
    }
    
}
