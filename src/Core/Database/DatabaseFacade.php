<?php 

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Session;

abstract class DatabaseFacade {

    public static function getSessionInstance():Database {
        if(Session::get('RBDatabaseFacade') === null) {
            Session::set('RBDatabaseFacade', Database::getInstance());
            return Database::getInstance();
        }
        return Session::get('RBDatabaseFacade');
    }
    
    public static function __callStatic($name, $arguments)
    {
        try {
                        
            return call_user_func_array(array(self::getSessionInstance(), $name), $arguments);

        } catch (\Exception $e) {

            Debug::log($e->getMessage(), [], 'DatabaseFacade.Exception','DatabaseFacade');
            
        }
    }

}