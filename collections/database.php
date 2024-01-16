<?php 

$localhost = [
    'server'    => '127.0.0.1',
    'login'     => 'root',
    'senha'     => '123',
    'database'  => 'databasename',
    'prefixo'   => 'dbnamev1_',
    'logs'      => 'logAll', //logAll logErrors logSuccess
];

//Or can use:
return \RBFrameworks\Core\Database::path_info('mysql://user:password@host:port?database_name|prefixo')['config'];

//sample
return \RBFrameworks\Core\Database::path_info('mysql://root:123@127.0.0.1:3306?databasename|dbnamev1_')['config'];

return $localhost ;