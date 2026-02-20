<?php

namespace RBFrameworks;

use RBFrameworks\Core\InputUser as RBInputUser;

class InputUserOptions {

    public mixed $default;
    public bool $getFromAnywhere;
    public bool $decodeUTF8;
    public bool $sanitize;

    public function __construct() {
        $this->default = '';
        $this->getFromAnywhere = true;
        $this->decodeUTF8 = true;
        $this->sanitize = true;
    }

}

class InputUser {

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

    public static function getFieldBool(string $name, bool $default = false):bool {
        $result = self::getFieldText($name, $default ? '1' : '0');
        if($result === 'true' or $result === true or $result === 1 or $result === '1') return true;
        if($result === 'false' or $result === false or $result === 0 or $result === '0') return false;
        return $default;
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