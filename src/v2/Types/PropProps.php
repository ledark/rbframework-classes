<?php 

//Created: 2021-11-27
namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Types\Php\Common;
use RBFrameworks\Core\Utils\Arrays;

/**
 * $myPropList = new PropProps('myFieldKey', [myProps]);
 */
class PropProps implements TypeInterface , PropInterface{

    private $name;
    private $value = [];

    public function __construct(string $name, array $props = []) {
        $this->name = $name;
        foreach ($props as $index => $value) {
            if(Common::getTypeFromMixed($value) == 'array') {
                if(Arrays::countElements($value) == 1) {
                    $index = key($value);
                    $value = $value[key($value)];
                }
            }
            if(Common::getTypeFromMixed($value) == 'object') {
                if($value instanceof TypeInterface and $value instanceof PropInterface) {
                    $index = $value->getPropName();
                    $value = $value->getPropValue();

                } else {
                    
                    $value = (new Common($value))->getString();
                }
            }
            $value = new PropMixed($index, $value);
            $this->value[$index] = $value->getValue();
        }        
    }

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted():array {
        return [$this->name => $this->value];
    }

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int {
        return count($this->value);
    }

    //getString para extrair um valor string do Type
    public function getString():string {
        return json_encode($this->value);
    }

    public function getValue():array {
        return $this->value;
    }

    public function getPropName(): string {
        return $this->name;
    }
    public function getPropValue() {
        return $this->getValue();
    }

    //Builders
    public static function buildFormatted(string $field, mixed $value = null):array {
        $instance = new self($field, $value);
        return $instance->getFormatted();
    }

    public static function buildValue(string $field, mixed $value = null):array {
        $instance = new self($field, $value);
        return $instance->getValue();
    }

    public static function buildFromArray(string $name, array $props):PropProps {
        $instance = new self($name, $props);
        return $instance;
    }

}