<?php

use Framework\Input as InputForm;

//Getting All in Array Form
function from_get():array {                 $input = InputForm::getInstance(); return $input->getFromGET()->data;    }
function from_post():array {                $input = InputForm::getInstance(); return $input->getFromPOST()->data;    }
function from_php():array {                 $input = InputForm::getInstance(); return $input->getFromPHP()->data;    }
function from_session():array {             $input = InputForm::getInstance(); return $input->getFromSESSION()->data;    }
function from_anywhere():array {            $input = InputForm::getInstance(); return $input->getFromAnywhere()->data;    }
function from_headers():array {             $input = InputForm::getInstance(); return $input->getFromHeaders()->data;    }
function from_uri(int $part = -1):string {  return InputForm::getFromUri($part); }

//Getting Specific Fields
function from_field_number(string $name, int $default = 0):int { return InputForm::getFieldNumber($name, $default); }
function from_field_text(string $name, string $default = ''):string { return InputForm::getFieldText($name, $default); }
function from_field_array(string $name, array $default = []):array { return InputForm::getFieldArray($name, $default); }
function from_field_textarea(string $name, string $default = ''):string { return InputForm::getFieldTextarea($name, $default); }