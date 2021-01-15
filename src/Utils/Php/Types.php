<?php

/**
 * Exemplos de Utilizaчуo:
 * 
 * use RBFrameworks\Utils\Php;
 * 
 * $foo = true;
 * Php\Type::onBoolean($foo, function(){ echo "Щ Booleano"; }, function(){ echo "Nуo щ Booleano"; }); //Exibirс Щ Booleano
 * 
 * $then = function() { "Щ booleano, sim!" };
 * $else = function() { "Nуo щ booleano, nуo!" };
 * $bar = 'true';
 * Php\Type::onBoolean($bar, $then, $else ); //Nуo щ booleano, nуo!
 * Php\Type::onBooleanExpanded($bar, $then, $else }); //Щ booleano, sim!
 * Php\Type::onBooleanExpanded('false', $then, $else }); //Щ booleano, sim!
 */

namespace RBFrameworks\Utils\Php;

class Type {

    public static function ifBoolean($mixedVariable, callable $then = null, callable $else = null) {
        self::onBoolean($mixedVariable, $then, $else);
    }
    public static function onBoolean($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'boolean') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function onBooleanExpanded($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'boolean' or strtolower($mixedVariable) == 'true' or strtolower($mixedVariable) == 'false') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifInteger($mixedVariable, callable $then = null, callable $else = null) {
        self::onInteger($mixedVariable, $then, $else);
    }
    public static function onInteger($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'integer') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifFloat($mixedVariable, callable $then = null, callable $else = null) {
        self::onFloat($mixedVariable, $then, $else);
    }
    public static function onFloat($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'double') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifDouble($mixedVariable, callable $then = null, callable $else = null) {
        self::onDouble($mixedVariable, $then, $else);
    }
    public static function onDouble($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'double') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifString($mixedVariable, callable $then = null, callable $else = null) {
        self::onString($mixedVariable, $then, $else);
    }
    public static function onString($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'string') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifArray($mixedVariable, callable $then = null, callable $else = null) {
        self::onArray($mixedVariable, $then, $else);
    }
    public static function onArray($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'array') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifObject($mixedVariable, callable $then = null, callable $else = null) {
        self::onObject($mixedVariable, $then, $else);
    }
    public static function onObject($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'object') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifResource($mixedVariable, callable $then = null, callable $else = null) {
        self::onResource($mixedVariable, $then, $else);
    }
    public static function onResource($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'resource') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function ifNULL($mixedVariable, callable $then = null, callable $else = null) {
        self::onNULL($mixedVariable, $then, $else);
    }
    public static function onNULL($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'NULL') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }
    public static function if_unknown($mixedVariable, callable $then = null, callable $else = null) {
        self::on_unknown($mixedVariable, $then, $else);
    }
    public static function on_unknown($mixedVariable, callable $then = null, callable $else = null) { 
        if(gettype($mixedVariable) == 'unknown') { 
            if(!is_null($then)) $then(); 
        } else {
            if(!is_null($else)) $else();
        }
    }

}

