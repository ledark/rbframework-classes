<?php 

//Created: 2021-11-27
namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Types\Php\Common;

class PropString implements TypeInterface, PropInterface{

    private $name;
    private $value;

    public function __construct(string $name, mixed $value = null) {
        $this->name = $name;
        $this->value = self::forceString($value);
    }

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted():array {
        return [$this->name => $this->value];
    }

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int {
        return -1;
    }

    //getString para extrair um valor string do Type
    public function getString():string {
        return (string) $this->value;
    }

    public static function forceString(mixed $mixed):string {
        $mixed = new Common($mixed);
        return $mixed->getString();
    }

    public function getValue():string {
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

    public static function buildValue(string $field, mixed $value = null):string {
        $instance = new self($field, $value);
        return $instance->getString();
    }    

}