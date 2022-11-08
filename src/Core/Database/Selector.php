<?php 

namespace RBFrameworks\Core\Database;

abstract class Selector {

    /**
     * use RBFrameworks\Database\Selector;
     * Selector::select($config1, $config2, $config3); //return array valid config
     */
    public static function select():array {
        $errorCount = 0;
        $args = func_get_args();
        foreach($args as $config) {
            try {
                if(!array($config)) {
                    $errorCount++;
                    continue;
                }
                new \PDO('mysql:host='.$config['server'].';dbname='.$config['database'], $config['login'], $config['senha']);
                return $config;
            } catch (\PDOException $e) {
                $errorCount++;
            }
        }
        if($errorCount === count($args)) {
            throw new \Exception('No connection available');
        }        
    }

}