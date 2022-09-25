<?php 

namespace RBFrameworks\Core\Types;

class Cep {

    protected $_value;
    
    public function __construct($value) {
        $this->_value = $value;
    }

    public function getNumber():string {
        if(isset($this->number)) return $this->number;
        $this->number = preg_replace('/\D/', '', $this->_value);
        return $this->number;
    }

    public function getFormatted():string {
        if(isset($this->formatted)) return $this->formatted;
        $this->formatted = $this->getNumber();
        $this->formatted = substr($this->formatted, 0, 5).'-'.substr($this->formatted, 5);
        return $this->formatted;
    }
    public function isValid():bool {
        if(empty($this->getNumber())) return false;
        if(in_array($this->getNumber(), ['00000000', '11111111', '22222222', '33333333', '44444444', '55555555', '66666666', '77777777', '88888888', '99999999'])) return false;
        //if(strlen($this->getNumber()) == 5) $this->number.= '000';
        return strlen($this->getNumber()) == 8 ? true: false;
    }

    public function getDetail():array {
        return (new \RBFrameworks\Core\Http("https://midiacriativa.com/cep/busca-json.php?cep=".$this->getFormatted()))->getJsonResponse();
    }

}