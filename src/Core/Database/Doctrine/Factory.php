<?php

namespace RBFrameworks\Core\Database\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Users\Database\Dados as UserDados;
use RBFrameworks\Core\Debug;

class Factory
{
    public static function getConnectionParams():array {
        $genericDatabase = new \RBFrameworks\Core\Database();
        return [
            'driver'      => 'pdo_mysql',
            'dbname'      => $genericDatabase->getConfigDatabase(),
            'user'        => $genericDatabase->getConfigUser(),
            'password'    => $genericDatabase->getConfigPass(),
            'host'        => $genericDatabase->getConfigHost(),
            'pdo'         => $genericDatabase->getPDOInstance(),
        ];  
    }
}
