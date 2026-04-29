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

    /*
    public static function encrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'r55i33c54mi9d61ia1';
        $secret_iv = 'R3i4C11aKRd0O';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;				
    }
    
    public static function decrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'r55i33c54mi9d61ia1';
        $secret_iv = 'R3i4C11aKRd0O';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;					
    }
    */
    public static function encrypt(string $string, string $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp'):string {
        $method = 'aes-128-cbc';
        $ivLength = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $qEncoded = openssl_encrypt($string, $method, $cryptKey, 0, $iv);
        return base64_encode($iv . $qEncoded);
    }

    public static function decrypt(string $string, string $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp'):string {
        $method = 'aes-128-cbc';
        $ivLength = openssl_cipher_iv_length($method);
        $data = base64_decode($string);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        $qDecoded = openssl_decrypt($encrypted, $method, $cryptKey, 0, $iv);
        return $qDecoded;
    }

}