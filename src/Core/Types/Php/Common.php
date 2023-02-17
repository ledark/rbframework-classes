<?php

namespace RBFrameworks\Core\Types\Php;

class Common
{
    private $originalValue = null;
    private $_value = null;
    private $type = null;

    public function __construct($mixedValue) {
        $this->originalValue = $mixedValue;
        $this->_value = $mixedValue;
        $this->type = self::getTypeFromMixed($mixedValue);
    }

    public function getOriginalValue() {
        return $this->originalValue;
    }

    public function getValue() {
        return $this->_value;
    }

    public static function getTypeFromMixed($mixed) {
        switch (gettype($mixed)) {
            case "boolean":             return 'bool';              break;
            case "integer":             return 'int';               break;
            case "double":              return 'float';             break;
            case "float":               return 'float';             break;
            case "string":              return 'string';            break;
            case "array":               return 'array';             break;
            case "object":              return 'object';            break;
            case "resource":            return 'resource';          break;
            case "resource (closed)":   return 'resource';          break;
            case "NULL":                return 'null';              break;
            case "unknown type":        return 'null';              break;
        }
    }

    public function getNumber():int {
        switch($this->type) {
            case 'bool':
                return $this->getValue() == true ? 1 : 0;
            break;
            case 'int':
                return $this->getValue();
            break;
            case 'float':
                return $this->getValue();
            break;
            case 'string':
                return intval($this->getValue());
            break;
            case 'array':
                return count($this->getValue()) *-1;
            break;
            case 'object':
                return count($this->getValue()) *-1;
            break;
            case 'resource':
                return -2;
            break;
            case 'null':
                return -1;
            break;
        }
    }    

    public function getString():string {
        switch($this->type) {
            case 'bool':
                return $this->getValue() == true ? 'true' : 'false';
            break;
            case 'int':
                return "".$this->getValue()."";
            break;
            case 'float':
                return "".$this->getValue()."";
            break;
            case 'string':
                return $this->getValue();
            break;
            case 'array':
                return json_encode($this->getValue());
            break;
            case 'object':
                return serialize($this->getValue());
            break;
            case 'resource':
                return base64_encode(serialize($this->getValue()));
            break;
            case 'null':
                return "";
            break;
        }
    }

    public function getBool():bool {
        $mixed = $this->getValue();
        if(is_bool($mixed)) return (bool) $mixed;
        if(is_numeric($mixed)) return (bool) $mixed;
        if(is_string($mixed)) {
            $mixed = strtolower($mixed);
            if($mixed == 'true') return true;
            if($mixed == 'false') return false;
            if($mixed == '1') return true;
            if($mixed == '0') return false;
            if($mixed == 'yes') return true;
            if($mixed == 'no') return false;
            if($mixed == 'y') return true;
            if($mixed == 'n') return false;
            if($mixed == 'sim') return true;
            if($mixed == 'nao') return false;
            if($mixed == 's') return true;
            if($mixed == 'n') return false;
        }
        return false;        
    }

}
