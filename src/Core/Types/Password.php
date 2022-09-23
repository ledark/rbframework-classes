<?php 

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Exceptions\CoreTypeException as Exception;

class Password {

    protected $_value;

    /**
     * in login forms get the userInput: 
     *  $senhaDigitada = get_input()[senha];
     * 
     * in database, search for:
     * senha = (new Password($senhaDigitada))->getEncrypted();
     * 
     * another way: $senhaSalva = $database->queryFirstRow(SELECT senha...)
     * $senha = (new Password($senhaDigitada))->hasMatch($senhaSalva)
     */

    public const MODE_INSECURE = 1;
   
    public function __construct($value, int $mode = self::MODE_INSECURE) {
        $this->validate($value);
        $this->_value = $value;
    }

    private function validate($value) {
        if(strlen($value) < 8) throw new Exception("Por favor, informe uma senha com no mínimo 8 caracteres.");
    }

    public function hasMatch(string $hash):bool {
        return password_verify($this->getValue(), $hash);
    }

    public function getValue():string {
        return $this->_value;
    }

    public function getEncrypted():string {
        return password_hash($this->getValue(), PASSWORD_DEFAULT);
    }    

}