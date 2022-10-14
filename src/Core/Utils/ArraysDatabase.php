<?php 

namespace RBFrameworks\Core\Utils;

use RBFrameworks\Core\Utils\Arrays;

trait ArraysDatabase {

    /**
     * extractFields extrair campo de uma array associativa ['field' => 'value', 'field2' => 'value', 'field3' => 'value'] 
     * para field1, field2, field3. Você pode passar um campo com * para não retornar `delimitado`.
     * @param array $dados
     * @return string
     */
    public static function extractFields(array $dados, string $return = ''): string {
        if(!self::is_assoc($dados)) {
            $arr = [];
            foreach($dados as $campo){
                $arr[$campo] = $campo;
            }
            $dados = $arr;
        }
        foreach($dados as $campo => $valor){
            $return.= (strpos($campo, '*') !== false) ? "$campo, " : "`$campo`, ";
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
     * para %s_field, %s_field2, %i_field3
     * @param array $dados
     * @return string
     */    
    public static function extractBindNamedParams(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $prefix = is_int($valor) ? 'i_' : 's_';
            $return.= "%{$prefix}{$campo}, ";
        }
        return self::sqlTrim($return);
    }

    public static function extractBindNamedParamsWithValues(array $dados): array {
        $binded = [];
        foreach($dados as $campo => $valor){
            $prefix = is_int($valor) ? 'i_' : 's_';
            $binded["%{$prefix}{$campo}"] = $valor;
        }
        return $binded;
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

    public static function extractUpdateBindedMeekro(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $prefix = is_int($valor) ? 'i_' : 's_';
            $return.= "`$campo` = "."%{$prefix}{$campo}".", ";
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
            $operator = self::findComparisonOperators($valor);
            $return.= "`$campo` $operator $valor AND ";
        }
        return self::sqlTrim($return);
    }
    
    public static function extractWhereOr(array $dados, string $return = ''): string {
        foreach($dados as $campo => $valor){
            $valor = self::sanitize($valor);
            $operator = self::findComparisonOperators($valor);
            $return.= "`$campo` $operator $valor OR ";
        }
        return self::sqlTrim($return);
    }
    
    public static function sqlTrim(string $query) {
        $query = rtrim($query, ", ");
        $query = rtrim($query, "AND ");
        $query = rtrim($query, "OR ");
        return $query;
    }

    public static function findComparisonOperators(string &$fieldValue = null):string {
        $fieldValue = is_null($fieldValue) ? "" : $fieldValue;
        $fieldValue = trim($fieldValue);
        
        
        //Primeiros Caracteres
        $subCharOne = substr($fieldValue, 0, 1);
        $subCharTwo = substr($fieldValue, 0, 2);
        $subCharThree = substr($fieldValue, 0, 3);

        $basicOperators = [
            '<='      => function(string $val) { return preg_replace('/\D/', '', $val); },
            '>='      => function(string $val) { return preg_replace('/\D/', '', $val); },
            '!='      => function(string $val) { return preg_replace('/\D/', '', $val); },
            '<'       => function(string $val) { return preg_replace('/\D/', '', $val); },
            '>'       => function(string $val) { return preg_replace('/\D/', '', $val); },
            '='       => function(string $val) { return preg_replace('/\D/', '', $val); },
            'LIK'     => function(string $val) { return "$val"; },
        ];
        
        foreach($basicOperators as $operator => $modifier) {
            if($operator == strtoupper($subCharThree)) {
                $fieldValue = $modifier($fieldValue);
                return $operator;
            }
            if($operator == strtoupper($subCharTwo)) {
                $fieldValue = $modifier($fieldValue);
                return $operator;
            }            
            if($operator == strtoupper($subCharOne)) {
                $fieldValue = $modifier($fieldValue);
                return $operator;
            }            
        }

        $fieldValue = "'$fieldValue'";
        return '=';
    }

    public static function sanitize($inp) {
        return Arrays::sanitize($inp);
    }
    
}