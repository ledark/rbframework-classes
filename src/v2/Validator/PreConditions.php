<?php

namespace RBFrameworks\Core\Validator;

class PreConditions
{
    public static function any(array $conditions):bool {
        foreach($conditions as $condition) {
            if($condition === true) return true;
        }
        return false;
    }

    public static function all(array $conditions):bool {
        foreach($conditions as $condition) {
            if($condition === false) return false;
        }
        return true;
    }

}
