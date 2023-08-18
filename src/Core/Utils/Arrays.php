<?php 

namespace RBFrameworks\Core\Utils;

/**
 * Updated in: 10-09-2022
 */

abstract class Arrays {

    use ArraysDatabase;    

    public static function setValueByKey(string $key, array &$data, $newValue = null) {

        if (!is_string($key) || empty($key) || !count($data)) {
            return false;
        }
        
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $code = "if(!isset( \$data['".implode("']['", $keys)."'] )) { \$data['".implode("']['", $keys)."'] = \$newValue; return true; }";
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
   
    /**
     * @testFunction testArraysIs_assoc
     */
    public static function is_assoc(array $arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @testFunction testArraysSanitize
     */
    public static function sanitize($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }     
    
    /**
     * @testFunction testArraysExtractKeysFromAssocArray
     */
    public static function extractKeysFromAssocArray(array $model, string $filter = 'mysql') {
        $validFields = [];
        foreach($model as $field => $props) {
            if(isset($props[$filter]) and !empty($props[$filter])) {
                $validFields[] = $field;
            } else if(is_numeric($field) and is_array($props)) {
                $validFields = array_merge($validFields, self::extractKeysFromAssocArray($props, $filter));
            } else {
                $validFields[] = $field;
            }
        }
        return $validFields;
    }

    public static function getValueByDotKey(string $key, array $data, $default = null, $separator = '.') {
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
                if(is_string($data)) return $default;
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
    
    public static function setValueByDotKey(string $key, array &$data, $overwriteValue = null, $separator = '.'):array {
        // @assert $key is a non-empty string
        // @assert $data is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($data))
        {
            return null;
        }
    
        // @assert $key contains a dot notated string
        if (strpos($key, $separator) !== false)
        {
            $keys = explode($separator, $key);
            $code = "\$data['".implode("']['", $keys)."'] = \$overwriteValue; return \$data; ";
            eval($code);            
    
            foreach ($keys as $innerKey)
            {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if(is_string($data)) {
                    return null;
                }

                /*
                if (!array_key_exists($innerKey, $data))
                {
                    $data[$innerKey] = $overwriteValue;
                }
    
                $data[$innerKey] = $overwriteValue;
                */
            }
    
            return $data;
        }
    
        // @fallback returning value of $key in $data or $default value
        $data[$key] = $overwriteValue;
        return $data;
    }  

    /**
     * array_isAssoc Exemples
     * var_dump(isAssoc(['a', 'b', 'c'])); // false
     * var_dump(isAssoc(["0" => 'a', "1" => 'b', "2" => 'c'])); // false
     * var_dump(isAssoc(["1" => 'a', "0" => 'b', "2" => 'c'])); // true
     * var_dump(isAssoc(["a" => 'a', "b" => 'b', "c" => 'c'])); // true
     */
    public static function isAssoc(array $arr):bool {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    public static function countElements(array $arr):int {
        $count = 0;
        $keys = array_keys($arr);
        for($i = 0; $i < count($arr); $i++) {
            $count++;
            if(is_array($keys[$i])) {
                foreach($arr[$keys[$i]] as $key => $value) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
}