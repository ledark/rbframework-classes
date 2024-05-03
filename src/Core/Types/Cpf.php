<?php 

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Exceptions\CoreTypeException as Exception;

class Cpf implements TypeInterface {

    protected $_value;
    protected $_throwException;
    
    public function __construct(string $value, bool $throwException = true) {
        try {        
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

        //Validacoes Basicas
        if(strlen($value) != 11) throw new Exception("CPF invalid format");
        if(!ctype_digit($value)) throw new Exception("CPF invalid type");

        //Erros mais Comuns
        if($value == '00000000000') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '11111111111') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '22222222222') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '33333333333') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '44444444444') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '55555555555') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '66666666666') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '77777777777') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '88888888888') throw new Exception("Por favor, informe um numero de CPF valido");
        if($value == '99999999999') throw new Exception("Por favor, informe um numero de CPF valido");

        if(!$this->validade2($value)) throw new Exception("Por favor, verifique seu CPF");

        return $value;
    }

    public function getNumber():int {
        return (int) $this->_value;
    }

    public function getFormatted():string {
        return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($this->_value));
    }

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

    private function validade2($cpf):bool {
        // Extrai somente os n�meros
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
            
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequ�ncia de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
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

    public function __toString() {
        return $this->_value;
    }

    public function getString():string {
        return (string) $this->_value;
    }

    public function getValetring() {
        return $this->_value;
    }

    public function getValue() {
        return $this->_value;
    }

}