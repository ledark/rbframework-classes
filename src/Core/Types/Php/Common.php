<?php

namespace RBFrameworks\Core\Types\Php;

class Common
{
    private $originalValue = null;
    private $_value = null;

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

}
