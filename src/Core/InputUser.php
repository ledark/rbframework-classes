<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Input;
use RBFrameworks\Core\Utils\Strings;
use RBFrameworks\Core\Utils\Encoding;

/**
 * Diversos Utilitário para tratar Input do Usuário
 * $userInput = new InputUser();
 * $userInput
 *  ->getFromPOST() //getFrom anywhere:     ->getFromPHP() for php://input ->getFromGET() ->getFromSESSION() ->getFromHeaders()
 *  ->unsetFields(['btnEnviar']) //ignore fields that are not necessary
 *  ->unsetFieldsThatAreNotInList(['campo1', 'campos]) //or use onlune fields that are necessary
 *  ->sanitize() //apply mysql_real_escape_string
 *  ->customSanitizeField('nome', function($value) { return strtoupper($value); }) //apply custom sanitize
 *  ->customSanitizeFields(['campo1', 'campo2'], function($value) { return strtoupper($value); }) //apply custom sanitize])
 *  ->encodeUTF8() //or decode with ->decodeUTF8()
 * 
 *  ->preventNullFields(['campo1', 'campo2']) //to throw exception if any of these fields are not setted
 *  ->preventEmptyFields(['campo1', 'campo2']) //to throw exception if any of these fields are empty (or not setted)
 * 
 *  ->validateField('campo1', function($value) { 
 *   // yor custom rules here that return false to throw exception 
 *  }, 'your custom message here when false on callback')
 *  
 * ->debugFields() //to throw a exception whit all fields in any point
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

    /**
     * static getInputUser
     * @return object clean
     */
    public static function getInputUser():object {
        return new self();
    }

    /**
     * static getSanitizedInputUser
     * @param array $assignedAssocValues sample: ['param1' => 'value1', 'param2' => 'value2']
     * @return object
     */
    public static function getSanitizedInputUser(array $assignedAssocValues):object {
        $input = new self();
        $input->sanitize();
        foreach($assignedAssocValues as $field => $value) {
            $input->assigned($field, $value);
        }        
        return $input;
    }

    /**
     * static getSanitizedResult
     * @param array $assignedAssocValues sample: ['param1' => 'value1', 'param2' => 'value2']
     * @sample: InputUser::getSanitizedResult(['param1' => 'value1', 'param2' => 'value2']); //return array
     * @return array
     */
    public static function getSanitizedResult(array $assignedAssocValues):array {
        $input = new self();
        $input->sanitize();
        foreach($assignedAssocValues as $field => $value) {
            $input->assigned($field, $value);
        }
        return $input->getResult();        
    }

    public function sanitize():object {
        $this->data = Strings::mysql_escape_mimic($this->data);
        return $this;
    }

    public function debugFields() {
        throw new \Exception("debuggedFields: -- '".implode("', '", array_keys($this->data))."' -- ".Debug::getPrintableAsText($this->data));
    }

    public function unsetFields(array $fields):object {
        foreach($fields as $field) {
            if(isset($this->data[$field])) unset($this->data[$field]);
        }
        return $this;
    }

    public function unsetFieldsThatAreNotInList(array $fields):object {
        foreach($this->data as $field => $value) {
            if(!in_array($field, $fields)) unset($this->data[$field]);
        }
        return $this;
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
    
    /**
     * assigned function para atribuir um valor a um campo que não exista
     *
     * @param string $field
     * @param string $value
     * @return object
     */
    public function assigned(string $field, string $value):object {
        if(!isset($this->data[$field])) {
            $this->data[$field] = $value;
        }
        return $this;
    }

    /**
     * set function para atribuir um valor a um campo independente se existe ou não
     *
     * @param string $field
     * @param string $value
     * @return object
     */
    public function set(string $field, string $value):object {
        $this->data[$field] = $value;
        return $this;
    }

    //FinalData
    public function getResult():array {
        return $this->data;
    }
}