<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Types\Json;

trait Modelv1 {
    
    use Configs;
    use Model;

    public function getModel():array {
        return $this->model;
    }

    /**
     * Esperado os values no formato [field => value]
     * @param array $values
     */
    public function humanize(array $values, callable $filter = null):array {
        if(is_string(key($values))) {
            $values = [0 => $values];
        }
        $result = [];
        foreach($values as $index => $row) {
            foreach($row as $field => $value) {
                if(isset($this->model[$field]) and isset($this->model[$field]['humanize'])) {
                    call_user_func_array($this->model[$field]['humanize'], [&$value, $field, &$row]);
                }
                if(is_object($filter)) {          
                call_user_func_array($filter, [&$value, $field, &$row]);
                }
                if(is_null($value)) {
                    unset($row[$field]);
                } else {
                    $row[$field] = $value;
                }
            }
            $result[] = $row;
        }
        return $result;
    }

    public function getPreviousField(string $field) {
        $keys = array_keys($this->model);
        $found_index = array_search($field, $keys);
        if ($found_index === false || $found_index === 0) return false;
        return $keys[$found_index-1];
    }   
    
    
    /**
     * Transforma um array contendo um model estilo novo para um antigo e retorna o model antigo
     * @param array $new
     * @return array
     */
    
    public static function new2old($new) {
        $model = array();
        foreach($new as $field => $props) {
            Encoding::DeepEncode($props);
            if(!isset($props['mysql'])) continue;
            if(empty($props['mysql'])) continue;
            $model[$field] = $props['mysql']." /* ".json_encode($props)." */";
        }
        return $model;
     }
     /**
      * Adiciona um parâmetro em um $model e devolve esse model com  o parâmetro adicionado
      * @param type $model
      * @param type $param
      * @param type $value
      */
     public static function addParam(&$model, $param, $value) {
                 
         //Adicionar Parâmetro em um $model old
         if(!is_array($model)) {
             $arr = self::getParams($model);
             $arr[$param] = $value;
             array_walk_recursive($arr, function(&$valor, $chave){
                 if(is_numeric($valor)) $valor = strval($valor);
             });
             $model = $arr['mysql']." /* ".self::json_encode_nice($arr)." */";            
         } else
 
         //Adicionar Parâmetro em um $model new
         if(is_array($model)) {
             $model[$param] = $value;
         }
         
     }
     
   
     public static function getParams($str) {
         $re = '~/\*(.*?)\*/~s';
         preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
         if(!count($matches)) {
             return array();
         } else {
             return self::json_decode_nice(($matches[1][0]), true);
         }        
     }
     
     public static function json_encode_nice($array, $options = null, $forceutf8 = true) {
         if($forceutf8) Encoding::DeepEncode($array);
         $stringjson = json_encode($array);
         if(substr($options, 'comment' !== false )) $stringjson = " /* $stringjson */ ";
         return $stringjson;
     }
     
     public static function json_decode_nice($json, $assoc = FALSE, $forceutf8 = true){
         if($forceutf8) $json = utf8_encode($json);
         $json = str_replace(array("\n","\r"),"",$json);
         $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
         $arr = json_decode($json,$assoc);
         if($forceutf8) Encoding::DeepDecode($arr);
         return $arr;
     }    
     
}
