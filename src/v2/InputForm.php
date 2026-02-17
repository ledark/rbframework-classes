<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\InputUser as RBInputUser;

if(!function_exists('inputform')) {
    function inputform() {
        return new InputUser();
    }
    //Getting All in Array Form
    function from_get():array {                 return InputForm::getFromGET();    }
    function from_post():array {                return InputForm::getFromPOST();    }
    function from_php():array {                 return InputForm::getFromPHP();    }
    function from_session():array {             return InputForm::getFromSESSION();    }
    function from_anywhere():array {            return InputForm::getFromAnywhere();    }
    function from_headers():array {             return InputForm::getFromHeaders();    }
    function from_uri(int $part = -1):string {  return InputForm::getFromUri($part); }

    //Getting Specific Fields
    function from_field_number(string $name, int $default = 0):int { return InputForm::getFieldNumber($name, $default); }
    function from_field_text(string $name, string $default = ''):string { return InputForm::getFieldText($name, $default); }
    function from_field_array(string $name, array $default = []):array { return InputForm::getFieldArray($name, $default); }
    function from_field_textarea(string $name, string $default = ''):string { return InputForm::getFieldTextarea($name, $default); }

}



class InputForm {

    /**
     * Você pode usar InputForm::getFieldNumber('nome_do_campo') para pegar o valor de um campo
     * Você pode usar InputForm::getFromPOST()['nome_do_campo'] para pegar o valor de um campo
     */

    use InputTrait;

    public static function getFieldNumber(string $name, int $default = 0):int {
        $options = new InputUserOptions();
        $options->default = $default;
        return (int) self::getField($name, $options);
    }

    public static function getFieldArray(string $name, array $default = []):array {
        $options = new InputUserOptions();
        $options->default = $default;
        return (array) self::getField($name, $options);
    }

    public static function getFieldText(string $name, string $default = ''):string {
        $options = new InputUserOptions();
        $options->default = $default;
        return (string) self::getField($name, $options);
    }

    public static function getFieldTextarea(string $name, string $default = ''):string {
        $options = new InputUserOptions();
        $options->sanitize = false;
        $options->default = $default;
        return (string) self::getField($name, $options);
    }

    public static function getField(string $name, InputUserOptions $options) {
       
        $inputUser = new RBInputUser();        

        if($options->getFromAnywhere) $inputUser->getFromAnywhere();
        if($options->decodeUTF8) $inputUser->decodeUTF8();
        if($options->sanitize) $inputUser->sanitize();

        $inputData = $inputUser->getResult();
        return isset($inputData[$name]) ? $inputData[$name] : $options->default;
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromPHP():array {
        return (new RBInputUser())->getFromPHP()->getResult();
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromGET():array {
        return (new RBInputUser())->getFromGET()->getResult();
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromPOST(bool $mergePHPInput = true):array {
        $post = (new RBInputUser())->getFromPOST()->getResult();
        if($mergePHPInput) $post = array_merge($post, self::getFromPHP());
        return $post;
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromSESSION():array {
        return (new RBInputUser())->getFromSESSION()->getResult();
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromHeaders():array {
        return (new RBInputUser())->getFromHeaders()->getResult();
    }

    /**
     * RBInputUser::getFrom
     */
    public static function getFromAnywhere():array {
        return (new RBInputUser())->getFromAnywhere()->getResult();
    }

    public static function getFromUri(int $part = -1):string {
        $uri = $_SERVER['REQUEST_URI'];         # /path/to/uri
        $uriParts = explode('/', $uri);
        if($part < 0) {
            return $uriParts[count($uriParts) + $part];
        } else {
            return isset($uriParts[$part]) ? $uriParts[$part] : '';
        }
    }

}