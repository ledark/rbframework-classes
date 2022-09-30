<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Input;
use RBFrameworks\Core\Utils\Strings;
use RBFrameworks\Core\Utils\Encoding;

/**
 * Diversos Utilitário para tratar Input do Usuário
 * $userInput = new InputUser();
 * $userInput
 *  ->getFromPOST()
 *  ->sanitize()
 *  ->encodeUTF8() //or decode...
 *  ->getResult()
 * ;
 */

class InputUser extends Input {

    public $instance;
    public $data;

    public function __construct() {
        $this->instance = new Input();
        $this->data = $this->instance->get();
    }

    public function sanitize():object {
        $this->data = Strings::mysql_escape_mimic($this->data);
        return $this;
    }

    public function debugFields() {
        throw new \Exception("debuggedFields: -- '".implode("', '", array_keys($this->data))."' -- ".Debug::getPrintableAsText($this->data));
    }

    /** Getters: getFrom... Chose one: PHP GET POST SESSION Headers */
    public function getFromPHP():object {
        $this->data = $this->instance->get();
        return $this;
    }
    public function getFromGET():object {
        $this->data = $this->instance->phpGet();
        return $this;
    }    
    public function getFromPOST():object {
        $this->data = $this->instance->phpPost();
        return $this;
    }
    public function getFromSESSION():object {
        $this->data = $this->instance->phpSession();
        return $this;
    }
    public function getFromHeaders():object {
        $this->data = $this->instance->phpRequestHeaders();
        return $this;
    }    

    /** Encode or Decode Things */
    public function encodeUTF8():object {
        Encoding::DeepEncode($this->data);
        return $this;
    }
    public function decodeUTF8():object {
        Encoding::DeepDecode($this->data);
        return $this;
    }

    /** Validators */
    public function preventNullFields(array $fields):object {
        foreach($fields as $field) {
            if(!isset($this->data[$field])) {
                throw new \Exception("O campo $field não existe");
            }
        }
        return $this;
    }
    public function preventEmptyFields(array $fields):object {
        self::preventNullFields($fields);
        foreach($fields as $field) {
            if(empty($this->data[$field])) {
                throw new \Exception("O campo {$field} não pode ser vazio");
            }
        }
        return $this;
    }

    public function validateField(string $field, callable $validator, string $errorMessage = null):object {
        $passed = $validator($this->data[$field]);
        if(!$passed) {
            if(is_null($errorMessage)) $errorMessage = "O campo {$field} não passou na validação";
            throw new \Exception($errorMessage);
        }
        return $this;
    }

    public function customSanitizeField(string $field, callable $callback):object {
        $this->data[$field] = $callback($this->data[$field]);
        return $this;
    }

    public function customSanitizeFields(array $fields, callable $callback):object {
        self::preventNullFields($fields);
        foreach($fields as $field) {
            $this->customSanitizeField($field, $callback);
        }
        return $this;
    }    

    //FinalData
    public function getResult():array {
        return $this->data;
    }
}