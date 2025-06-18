<?php

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Exceptions\CoreTypeException as Exception;

class Cnpj {

    protected $_value;
    public $number;
    public $formatted;
    public $untouched;
    public $_throwException;

    public function __construct(string $value, bool $throwException = true) {
        try {
            $this->untouched = $value;
            $this->_value = $this->validate($value);
            $this->_throwException = $throwException;
        } catch (Exception $e) {
            if($throwException) {
                throw new Exception($e->getMessage());
            } else {
                return false;
            }
        }
    }

    private function validate(string $value):string {

        //BasicPreparation
        $value = trim($value);
        $value = str_replace(' ', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace('-', '', $value);
        $value = str_replace('/', '', $value);
        $value = str_replace('\\', '', $value);

        //Validacoes Basicas 012.345.678/9012-34
        if(strlen($value) <= 13) throw new Exception("CNPJ invalid format");
        if(!ctype_digit($value)) throw new Exception("CNPJ invalid type");

        //Erros mais Comuns
        if($value == '00000000000000') throw new Exception("CNPJ invalid number");
        if($value == '11111111111111') throw new Exception("CNPJ invalid number");
        if($value == '22222222222222') throw new Exception("CNPJ invalid number");
        if($value == '33333333333333') throw new Exception("CNPJ invalid number");
        if($value == '44444444444444') throw new Exception("CNPJ invalid number");
        if($value == '55555555555555') throw new Exception("CNPJ invalid number");
        if($value == '66666666666666') throw new Exception("CNPJ invalid number");
        if($value == '77777777777777') throw new Exception("CNPJ invalid number");
        if($value == '88888888888888') throw new Exception("CNPJ invalid number");
        if($value == '99999999999999') throw new Exception("CNPJ invalid number");
        if($value == '000000000000000') throw new Exception("CNPJ invalid number");
        if($value == '111111111111111') throw new Exception("CNPJ invalid number");
        if($value == '222222222222222') throw new Exception("CNPJ invalid number");
        if($value == '333333333333333') throw new Exception("CNPJ invalid number");
        if($value == '444444444444444') throw new Exception("CNPJ invalid number");
        if($value == '555555555555555') throw new Exception("CNPJ invalid number");
        if($value == '666666666666666') throw new Exception("CNPJ invalid number");
        if($value == '777777777777777') throw new Exception("CNPJ invalid number");
        if($value == '888888888888888') throw new Exception("CNPJ invalid number");
        if($value == '999999999999999') throw new Exception("CNPJ invalid number");

        $this->_value = $value;

        if(strlen($value) == 14 and !$this->validDV()) {
            throw new Exception("CNPJ invalido");
        }


        return $value;
    }

    public function getNumber():int {
        return (int) $this->_value;
    }

    public function getFormatted():string {
        if(strlen($this->_value) == 14) {
            return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($this->_value));
        } else
        if(strlen($this->_value) == 15) {
            return vsprintf("%s%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($this->_value));
        }
        return is_null($this->_value) ? $this->untouched : $this->_value;
    }

    /*
    private function validDV():bool {
        $cpf = $this->_value;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    */

    private function validDV():bool {
        // Extrai os números
        $document = $this->_value;
        $cnpj = preg_replace('/[^0-9]/is', '', $document);

        // Valida tamanho
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica sequência de digitos repetidos. Ex: 11.111.111/111-11
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Valida dígitos verificadores
        for ($t = 12; $t < 14; $t++) {
            for ($d = 0, $m = ($t - 7), $i = 0; $i < $t; $i++) {
                $d += $cnpj[$i] * $m;
                $m = ($m == 2 ? 9 : --$m);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$i] != $d) {
                return false;
            }
        }
        return true;
    }

    public function __toString() {
        return $this->_value;
    }

}