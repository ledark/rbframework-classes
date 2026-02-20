<?php

namespace Framework\Input;
trait GetFromTrait {

    /** Getters: getFrom... Chose one: PHP GET POST SESSION Headers */
    public function getFromPHP():object {
        $this->data = $this->get();
        return $this;
    }
    public function getFromGET():object {
        $this->data = $this->phpGet();
        return $this;
    }
    public function getFromPOST():object {
        $this->data = $this->phpPost();
        return $this;
    }
    public function getFromSESSION():object {
        $this->data = $this->phpSession();
        return $this;
    }
    public function getFromHeaders():object {
        $this->data = $this->phpRequestHeaders();
        return $this;
    }

    public function getFromAnywhere():object {
        $this->data = array_merge($this->data, $this->get());
        $this->data = array_merge($this->data, $this->phpGet());
        $this->data = array_merge($this->data, $this->phpPost());
        $this->data = array_merge($this->data, $this->phpSession());
        $this->data = array_merge($this->data, $this->phpRequestHeaders());
        return $this;
    }

    public static function getFromUri(int $part = -1, string $return = 'self'):string {
        $uri = $_SERVER['REQUEST_URI'];         # /path/to/uri
        $uriParts = explode('/', $uri);
        if($return == 'offset' and $part  > 0) {
            for($i=0; $i<=$part; $i++) {
                array_shift($uriParts);
            }
            return implode('/', $uriParts);
        }
        if($part < 0) {
            return $uriParts[count($uriParts) + $part];
        } else {
            return isset($uriParts[$part]) ? $uriParts[$part] : '';
        }
    }

    public static function getFromFirstField(array $inputFields, callable $errCallback = null) {
        $inputData = self::getInstance()->get();
        if(!count($inputData)) return is_callable($errCallback) ? $errCallback() : $errCallback;
        foreach($inputFields as $field) {
            if(in_array($field, array_keys($inputData))) return $inputData[$field];
        }
        return is_callable($errCallback) ? $errCallback() : $errCallback;
    }

}