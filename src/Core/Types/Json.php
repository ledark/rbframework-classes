<?php 

//Created: 2021-11-27

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Types\Php\Common;

class Json implements TypeInterface {

    private $original_value;
    private $options = [];

    public function __construct( $value) {
        $this->original_value = $value;
    }

    public function setOption(string $key, string $value) {
        $this->options[$key] = $value;
    }

    public function getOption(string $key,  $default = null) {
         return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted() {
        return $this->getValue();
    }

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int {
        return -1;
    }

    //getString para extrair um valor string do Type
    public function getString($options = null):string {        
        if(isset($this->json_decoded)) return $this->json_decoded;
        $forceutf8 = $this->getOption('forceUtf8', true);
        $this->json_decoded = self::json_encode_nice($this->original_value, $options, $forceutf8);
        return $this->json_decoded;
    }

    public function getArray():array {
        if(isset($this->json_encoded)) return $this->json_encoded;
        $forceutf8 = $this->getOption('forceUtf8', true);
        $this->json_encoded = self::json_decode_nice($this->original_value, true, $forceutf8);
        return $this->json_encoded;
    }

    public function getObject():array {
        if(isset($this->json_encodedObj)) return $this->json_encodedObj;
        $forceutf8 = $this->getOption('forceUtf8', true);
        $this->json_encoded = self::json_decode_nice($this->original_value, false, $forceutf8);
        return $this->json_encodedObj;
    }


    //getValue para extrair um valor mixed
    public function getValue() {
        switch(Common::getTypeFromMixed($this->original_value)) {
            case 'array':
                return $this->getString();
            break;
            case 'string':
                return $this->getArray();
            break;
            default:
                throw new \Exception("value need to be array or string, ".Common::getTypeFromMixed($this->original_value)." is given.'");
            break;
        }
    }

    /*
    private function beforeProcess() {
        Plugin::load("utf8_encode_deep");
    }

    public function getEncodedFrom(array $json):string {
        $this->beforeProcess();
        if($this->getOption('forceUtf8') === true) {
            utf8_encode_deep($json);
        } else
        if($this->getOption('forceUtf8') === false) {
            utf8_decode_deep($json);
        }
    }
    public function getDecodedFrom(string $json):array {
        $this->beforeProcess();
        
    }
    */

    //Original Legacy Funcions
    public static function json_encode_nice($array, $options = null, $forceutf8 = true) {
        Plugin::load("utf8_encode_deep");
        if($forceutf8) utf8_encode_deep($array);
        $stringjson = json_encode($array);
        if(substr($options, 'comment' !== false )) $stringjson = " /* $stringjson */ ";
        return $stringjson;
    }

    public static function json_decode_nice($json, $assoc = FALSE, $forceutf8 = true){
        if($forceutf8) $json = utf8_encode($json);
        Plugin::load("utf8_encode_deep");
        $json = str_replace(array("\n","\r"),"",$json);
        $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
        $arr = json_decode($json,$assoc);
        if($forceutf8) utf8_decode_deep($arr);
        return $arr;
    }  

}