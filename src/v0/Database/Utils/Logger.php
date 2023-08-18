<?php

namespace RBFrameworks\Database\Utils;

class Logger extends \RBFrameworks\Database\Dao {
    
    public function __construct($name = 'logger') {
        
        $Database = new \RBFrameworks\Database('doDBv3');
        $Model = new \RBFrameworks\Database\Model([$name => [
            'cod'           => ['mysql' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY'],
            'group'         => ['mysql' => 'VARCHAR(255) NOT NULL'],
            'message'       => ['mysql' => 'LONGTEXT NOT NULL'],
            'context'       => ['mysql' => 'LONGTEXT NOT NULL'],
            'on'            => ['mysql' => 'INT(10) UNSIGNED NOT NULL'],
        ]]);
        
        parent::__construct($Database, $Model);
        
        $this->build();
    }
    
    public function add(string $message, string $group = 'info', array $context = []) {
        parent::add([
            'group'         => $group,
            'message'       => $message,
            'context'       => count($context) ? serialize($context) : '',
            'on'            => time(),
        ]);
    }
    
}
