<?php

namespace RBFrameworks\Storage;

use MeekroDB;

class Database {
    
    public $DB;
    public $prefixo = '';
    
    public function __construct($config = null) {
        $config = $this->extractConfig($config);
        $this->DB = new MeekroDB($config['server'], $config['login'], $config['senha'], $config['database']);
        $this->prefixo = $config['prefixo'];
    }
    
    public function __call (string $name, array $arguments) {
        return call_user_func_array(array($this->DB, $name), $arguments);
    }
    
    public static function __callStatic (string $name, array $arguments) {
        return call_user_func_array(array($this->DB, $name), $arguments);
        
    }
    
    private function extractConfig($mixed):array {
        switch(gettype($mixed)) {
            case 'array':
                if (array() === $mixed) {
                    //case simple_array
                    return [
                        'server' => $mixed[0],
                        'login' => $mixed[1],
                        'senha' => $mixed[2],
                        'database' => $mixed[3],
                        'prefixo' => $mixed[4],
                    ];
                } else
                if(array_keys($mixed) !== range(0, count($mixed) - 1)) {
                    //case assoc_array
                    return $mixed;
                }
            break;
            case "integer":
                throw new \Exception("Tipo integer n?o possui uma configura??o de banco de dados v?lida");
            break;
            case "double":
                throw new \Exception("Tipo float (ou double) n?o possui uma configura??o de banco de dados v?lida");
            break;
            case "string":
                return get_config($mixed);
            break;
            case "object":
                throw new \Exception("Tipo object n?o possui uma configura??o de banco de dados implementada");
            break;
            case "resource":
                throw new \Exception("Tipo object n?o possui uma configura??o de banco de dados implementada");
            break;
            case "NULL":
                return get_config('database');
            break;
            case "unknown type":
                throw new \Exception("Tipo desconhecido que n?o possui uma configura??o de banco de dados detecada");
            break;
        }
    }
  
}