<?php

namespace RBFrameworks\Utils;


abstract class Arrays {
    
    use Arrays\traitSQL;

    public static function setValueByKey(string $key, array &$data, $newValue = null) {

        if (!is_string($key) || empty($key) || !count($data)) {
            return false;
        }
        
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $code = "if(isset( \$data['".implode("']['", $keys)."'] )) { \$data['".implode("']['", $keys)."'] = \$newValue; return true; }";
            eval($code);
            return false;
        }
        
        if(array_key_exists($key, $data)) {
            $data[$key] = $newValue;
            return true;
        }
        
        return false;
    }

    public static function getValueByKey(string $key, array $data, $default = null, $separator = '.') {
    // @assert $key is a non-empty string
    // @assert $data is a loopable array
    // @otherwise return $default value
    if (!is_string($key) || empty($key) || !count($data))
    {
        return $default;
    }

    // @assert $key contains a dot notated string
    if (strpos($key, $separator) !== false)
    {
        $keys = explode($separator, $key);

        foreach ($keys as $innerKey)
        {
            // @assert $data[$innerKey] is available to continue
            // @otherwise return $default value
            if (!array_key_exists($innerKey, $data))
            {
                return $default;
            }

            $data = $data[$innerKey];
        }

        return $data;
    }

    // @fallback returning value of $key in $data or $default value
    return array_key_exists($key, $data) ? $data[$key] : $default;
}

    public static function __callStatic($name, $arguments) {
        return call_user_func_array(array(new SQL, $name), $arguments);
    }
    
    public static function is_assoc(array $arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private static function sanitize($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }     
    
    private static function extractKeysFromAssocArray(array $model, string $filter = 'mysql') {
        $validFields = [];
        foreach($model as $field => $props) {
            if(isset($props[$filter]) and !empty($props[$filter])) {
                $validFields[] = $field;
            }
        }
        return $validFields;
    }
    
}
