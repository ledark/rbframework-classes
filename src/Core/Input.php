<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Session;

class Input {

    private $input = [];
    private $headers = [];
    private static $instance = null;

    private function phpInput():array {
        $request_file = file_get_contents("php://input");

        if($request_file !== false) {
            $request_json = json_decode($request_file);
            if(is_null($request_json)) return [];
            return get_object_vars($request_json);
        } else {
            return [];
        }
    }

    public function phpPost():array {
        return $_POST;
    }

    public function phpGet():array {
        return $_GET;
    }

    public function phpSessionGet(string $key, $default = null) {
        new Session();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    public function phpSession():array {
        new Session();
        return isset($_SESSION) ? $_SESSION : array();
    }

    public function phpCreateHeaders(array $headers):bool {
        if(function_exists('apache_request_headers')) return false;
        $this->headers = array_merge($this->phpRequestHeaders(), $headers);
        return true;
    }

    public function phpRequestHeaders():array {
        if(function_exists('apache_request_headers')) return apache_request_headers();
        if(is_array($this->headers)) return $this->headers;
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
          if( preg_match($rx_http, $key) ) {
            $arh_key = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            // do some nasty string manipulations to restore the original letter case
            // this should work in most cases
            $rx_matches = explode('_', $arh_key);
            if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
              foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
              $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
          }
        }
        $this->headers = $arh;
        return( $arh );       
    }

    public function __construct() {
        $this->input = $this->phpInput();
        $this->input = !count($this->input) ? $this->phpPost() : $this->input;
        if(!count($this->input)) {
            $this->input = $this->phpGet();
        }
    }

    public function get(bool $forceUtf8 = null):array {
        if(is_null($forceUtf8)) return $this->input;
        if($forceUtf8) {
            Encoding::DeepEncode($this->input);
        } else {
            Encoding::DeepDecode($this->input);
        }
        return $this->input;
    }

    public static function getAll():array {
        return self::getInstance()->get();
    }

    public function decode():array {
        $_INPUT = [];
        foreach($this->get() as $chave => $valor) {
            $_INPUT[$chave] = is_string($valor) ? str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $valor) : $valor;
        }
        return $_INPUT;
    }

    public static function getInstance():object {
        if (self::$instance == null)
        {
            self::$instance = new Input();
        }        
        return self::$instance;
    }
    public static function hasGET():bool {
        return count(self::getInstance()->phpGet())  > 0 ? true : false;
    }
    public static function hasPOST(bool $strict = false):bool {
        $hasPost = count(self::getInstance()->phpPost());
        $hasInput = count(self::getInstance()->phpInput());
        if($hasPost > 0) return true;
        if($hasInput > 0 and !$strict) return true;
        return false;
    }
    public static function hasSESSION(int $moreThan = 1):bool {
        return count(self::getInstance()->phpSession())  > $moreThan ? true : false;
    }
    public static function hasHeader(string $name, $value = null):bool {
        $apache_request_headers = self::getInstance()->phpRequestHeaders();
        if(!isset($apache_request_headers[$name])) return false;
        if(is_null($value)) return true;
        if($apache_request_headers[$name] == $value) return true;
        return false;
    }
    public static function hasAuthorization(string $type, $value):bool {
        
        $sanitizeValue = function(string $value) {
            return filter_var($value, \FILTER_DEFAULT);
        };

        switch(strtoupper($type)) {
            case 'BEARER':
                return self::hasHeader('Authorization', "Bearer ".$sanitizeValue($value));
            break;
            case 'BASIC':
                if(is_array($value)) {
                    $login = $sanitizeValue($value[0]);
                    $senha = $sanitizeValue($value[1]);
                }
                return self::hasHeader('Authorization', "Basic ".base64_encode($login.':'.$senha));
            break;
        }
        
        return false;
    }

    public static function hasAllInputFields(array $inputRequirements):bool {
        $inputData = self::getInstance()->get();
        if(!count($inputData)) return false;
        foreach($inputRequirements as $field) {
            if(!in_array($field, array_keys($inputData))) return false;
        }
        return true;
    }

    public static function hasAnyInputFields(array $inputRequirements):bool {
        $inputData = self::getInstance()->get();
        if(!count($inputData)) return false;
        foreach($inputRequirements as $field) {
            if(in_array($field, array_keys($inputData))) return true;
        }
        return false;
    }

    public static function getFromFirstField(array $inputFields, callable $errCallback = null) {
        $inputData = self::getInstance()->get();
        if(!count($inputData)) return is_callable($errCallback) ? $errCallback() : $errCallback;
        foreach($inputFields as $field) {
            if(in_array($field, array_keys($inputData))) return $inputData[$field];
        }
        return is_callable($errCallback) ? $errCallback() : $errCallback;
    }

    public static function getField(string $inputField, $defaultValue = null) {
        $inputData = self::getInstance()->get();
        return isset($inputData[$inputField]) ? $inputData[$inputField] : $defaultValue;
    }

    /**
     * $input->needs([])
     */
    /*
    public function needs(array $inputRequirements, bool $decode = true, bool $encodeUtf8 = true):array {
        $strict = [];
        $dados = $decode ? $this->decode() : $this->get();
        if($encodeUtf8) {
           Plugin::load('utf8_encode_deep');
            utf8_decode_deep($dados);
        }
        foreach($inputRequirements as $key => $value) {
            if(!is_numeric($key) and !in_array($key, $dados)) throw new \Exception("Input $key is not valid");
            $field = is_numeric($key) ? $value : $key;
            $valor = is_numeric($key) ?
            $strict[$field] = $valor;
        }
        return $strict;
    }
    */

}