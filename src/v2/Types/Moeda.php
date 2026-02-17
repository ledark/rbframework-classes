<?php 

namespace RBFrameworks\Core\Types;

class Moeda {

    protected $_value;
    protected bool $_signed;
    private int $number;
    
    public function __construct($value) {
        $this->_value = $value;
        $this->_signed = $value < 0 ? true : false;
    }

    public function getDecimal() {
        return number_format($this->getNumber()/100, 2, '.', '');
    }
    public function getFormatted() {
        return number_format($this->getNumber()/100, 2, ',', '.');
    }
    public function getFullFormatted(string $prefix = 'R$ ') {
        return $prefix.$this->getFormatted();
    }

    public function getNumber():int {
        if(isset($this->number)) return $this->number;
        $this->number = preg_replace('/\D/', '', $this->_value);
        if($this->_signed) {
            $this->number = $this->number * -1;
        }
        return (int) $this->number;
    }

    public function increasePercentual(float $porcent) {
        $valor = $this->getNumber();
        $newValue = intval($valor + ($valor*$porcent));
        $this->number = $newValue;
    }
    public function decreasePercentual(float $porcent) {
        $valor = $this->getNumber();
        $newValue = intval($valor - ($valor*$porcent));
        $this->number = $newValue;
    }

}