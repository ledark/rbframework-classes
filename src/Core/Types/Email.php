<?php 

namespace RBFrameworks\Core\Types;
use RBFrameworks\Core\Exceptions\CoreTypeException as Exception;

class Email {

    protected $_value;
    protected $_isValid = false;
    
    public function __construct(string $value, bool $throwException = true) {

        try {
            $this->_value = $this->validate($value);
            $this->_isValid = true;
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
        $value = strtolower($value);

        //Validações Basicas
        if(!ctype_graph($value)) throw new Exception("E-mail Invalido");
        if(strpos($value, '@') === false) throw new Exception('Por favor, informe seu e-mail completo');
        if(strpos($value, '.') === false) throw new Exception('Por favor, informe seu e-mail completo');

        //Outras Validações
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))  throw new Exception("Por favor, verifique se o e-mail foi informado corretamente ");
        if(!$this->patternCheck($value)) throw new Exception("Por favor, verifique o e-mail informado");

        return $value;
    }

    private function patternCheck(string $value):bool {
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        return (bool) preg_match($pattern, $value);
    }

    public function __toString() {
        return $this->_value;
    }

    public function getValue():string {
        return $this->_value;
    }

    public function isValid():bool {
        return $this->_isValid;
    }

}