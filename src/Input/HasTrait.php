<?php

namespace Framework\Input;

trait HasTrait {

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
}