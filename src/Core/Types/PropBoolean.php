<?php 

//Created: 2021-11-27
namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Types\Php\Common;

class PropBoolean implements TypeInterface, PropInterface {

    private $name;
    private $value;

    public function __construct(string $name, mixed $value = null) {
        $this->name = $name;
        $this->value = self::forceBoolean($value);
    }

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted():array {
        return [$this->name => $this->value];
    }

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int {
        return $this->value === true ? 1 : 0;
    }

    //getString para extrair um valor string do Type
    public function getString():string {
        return $this->value === true ? 'true' : 'false';
    }

    public function getValue():bool {
        return (bool) $this->value;
    }

    public static function forceBoolean(mixed $mixed):bool {
        $mixed = new Common($mixed);

        $string = $mixed->getString();

        foreach(['y','s','yes','t','true'] as $bool) {
            if(strtolower($string) === $bool) return true;
        }        

        foreach(['n','n','no','f','false'] as $bool) {
            if(strtolower($string) === $bool) return false;
        }

        $number = $mixed->getNumber();
        return ($number > 0) ? true : false;
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

    public static function buildValue(string $field, mixed $value = null):bool {
        $instance = new self($field, $value);
        return $instance->getValue();
    }        

}