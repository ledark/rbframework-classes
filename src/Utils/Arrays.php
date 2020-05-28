<?php

namespace RBFrameworks\Utils;

abstract class Arrays {
    
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
}
