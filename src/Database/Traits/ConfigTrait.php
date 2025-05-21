<?php 

namespace Framework\Database\Traits;

use Framework\Config;

trait ConfigTrait {

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
                if(strpos($mixed, '://') !== false) {
                    //parse dsn
                    $dsn = parse_url($mixed);
                    $config = [
                        'server' => $dsn['host'],
                        'login' => $dsn['user'],
                        'senha' => $dsn['pass'],
                        'database' => substr($dsn['path'], 1),
                    ];
                }
                $config = Config::get($mixed);
                if(!is_array($config)) throw new \Exception("get_config dont found $mixed");
                return $config;
            break;
            case "object":
                throw new \Exception("Tipo object n?o possui uma configura??o de banco de dados implementada");
            break;
            case "resource":
                throw new \Exception("Tipo object n?o possui uma configura??o de banco de dados implementada");
            break;
            case "NULL":
                $config = Config::get('database');
                if(!is_array($config)) throw new \Exception("Config::get not found database");
                return $config;                
            break;
            case "unknown type":
                throw new \Exception("Tipo desconhecido que n?o possui uma configura??o de banco de dados detecada");
            break;
        }
    }    

}