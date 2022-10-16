<?php 

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Database;

abstract class DatabaseFacade {
    
    public static function __callStatic($name, $arguments)
    {
        try {
                        
            return call_user_func_array(array(Database::getInstance(), $name), $arguments);

        } catch (\Exception $e) {
            Debug::log($e->getMessage(), [], 'DatabaseFacade.Exception','DatabaseFacade');
        }
    }

}