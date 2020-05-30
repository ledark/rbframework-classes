<?php

namespace RBFrameworks\Helpers\Php;

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

    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para field1, field2, field3
     * @param array $dados
     * @return string
     */
    public static function extractFields(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $return.= "`$campo`, ";
        }
        return self::sqlTrim($return);
    }


    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para ?, ?, ?
     * @param array $dados
     * @return string
     */    
    public static function extractBindParams(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $return.= "?, ";
        }
        return self::sqlTrim($return);
    }
    
    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para 'value', 
     * @param array $dados
     * @return string
     */    
    public static function extractValues(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $return.= "'$valor', ";
        }
        return self::sqlTrim($return);
    }

    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para ['value', 'value', 'value']
     * @param array $dados
     * @return array
     */        
    public static function extractValuesAsArray(array $dados, array $return = []): array {
        foreach($dados as $campo => $valor){
            $return[] = $valor;
        }
        return $return;
    }

    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para field = ?, field2 = ?, field3 = ?
     * @param array $dados
     * @return string
     */
    public static function extractUpdateBinded(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $return.= "`$campo` = ?, ";
        }
        return self::sqlTrim($return);
    }

    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para field = 'value', field2 = 'value', ...
     * @param array $dados
     * @return string
     */    
    public static function extractUpdateRaw(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $valor = self::sanitize($valor);
            $return.= "`$campo` = '$valor', ";
        }
        return self::sqlTrim($return);
    }
    
    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para field = VALUES('value'), field = VALUES ('value'), ...
     * @param array $dados
     * @return string
     */            
    public static function extractUpsert(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $return.= "`$campo` = VALUES('$valor'), ";
        }
        return self::sqlTrim($return);
    }
    
    public static function extractWhereAnd(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $valor = self::sanitize($valor);
            $return.= "`$campo` = '$valor' AND ";
        }
        return self::sqlTrim($return);
    }
    
    public static function extractWhereOr(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $valor = self::sanitize($valor);
            $return.= "`$campo` = '$valor' OR ";
        }
        return self::sqlTrim($return);
    }
    
    private static function sqlTrim(string $query) {
        $query = rtrim($query, ", ");
        $query = rtrim($query, "AND ");
        $query = rtrim($query, "OR ");
        return $query;
    }

    private static function sanitize($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }     
    
}
