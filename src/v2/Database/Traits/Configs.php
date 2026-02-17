<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Config;

trait Configs {

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

    private function getNumDimensions(array $array):int {
        if (is_array(reset($array)))  {
            $return = $this->getNumDimensions(reset($array)) + 1;
        } else  {
            $return = 1;
        }
        return $return;
    }

    public function getDataSourceName():string {
        $db_host = $this->host;
        $db_name = $this->database;        
        return "mysql:host=$db_host;dbname=$db_name;";
    }

    public function getDSN():string {
        return $this->getDataSourceName();
    }

    public function getConfigDatabase():string { return $this->database;}
    public function getConfigHost():string { return $this->host;}
    public function getConfigUser():string { return $this->user;}
    public function getConfigPass():string { return $this->pass;}

    private static function getReplaces():array {
        return [
            '://' => '!protocol!',
            ':' => '!collon!',
            '@' => '!arroba!',
            '?' => '!question!',
        ];
    }

    // mysql://user:password@host:port?database_name|prefixo
    public static function path_info(string $database_path):array {

        //parse the string conn: user:password@host:port
        $parse = explode('?', $database_path, 2);
        $database_path = $parse[0];
        $database_name = $parse[1];
        unset($parse);

        $parse = explode('://', $database_path, 2);
        $protocol = $parse[0];
        $database_path = $parse[1];
        unset($parse);

        $parse = explode('@', $database_path, 2);
        $user_pass = $parse[0];
        $host_port = $parse[1];
        unset($parse);

        $user_pass = explode(':', $user_pass, 2);
        $user = $user_pass[0];
        $pass = $user_pass[1];

        $host_port = explode(':', $host_port, 2);
        $host = $host_port[0];
        $port = $host_port[1];

        //Descobrir Prefixo
        if(strpos($database_name, '|') !== false) {
            $parse = explode('|', $database_name, 2);
            $database_name = $parse[0];
            $prefixo = $parse[1];
            unset($parse);
        } else {
            $prefixo = '';
        }

        //Efetuar Replaces
        foreach(self::getReplaces() as $myChar => $myReplace) {
            $protocol = str_replace($myReplace, $myChar, $protocol);
            $user = str_replace($myReplace, $myChar, $user);
            $pass = str_replace($myReplace, $myChar, $pass);
            $host = str_replace($myReplace, $myChar, $host);
            $port = str_replace($myReplace, $myChar, $port);
            $database_name = str_replace($myReplace, $myChar, $database_name);
        }        

        //Retorna Resultado
        return [
            'original_path' => $database_path,
            'is_valid_path' => self::path_validate($database_path),
            'database_name' => $database_name,
            'protocol' => $protocol,
            'user' => $user,
            'pass' => $pass,
            'host' => $host,
            'port' => $port,
            'prefixo' => $prefixo,
            'config' => [
                'server'    => $host,
                'login'     => $user,
                'senha'     => $pass,
                'database'  => $database_name,
                'prefixo'   => $prefixo,
                'type'      => $protocol,
                'port' => $port,
            ],
        ];
    }

    private static function path_validate(string $database_path):bool {
        if(
            strpos($database_path, '://') === false or
            strpos($database_path, ':') === false or
            strpos($database_path, '@') === false or
            strpos($database_path, '?') === false
        ) {
            return false;
        }
        return true;
    }    

}