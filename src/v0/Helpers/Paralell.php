<?php

namespace RBFrameworks\Helpers;

class Paralell extends \RBFrameworks\Database\Dao {
    
    public function __construct($name = 'rbf_parallel_queues') {
    
        $Database = new \RBFrameworks\Database('doDBv3');
        $Model = new \RBFrameworks\Database\Model([$name => [
            'cod'          => ['mysql' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY'],
            'event'        => ['mysql' => 'VARCHAR(255) NOT NULL'],
            'type'         => ['mysql' => 'VARCHAR(255) NOT NULL'],
            'callback'     => ['mysql' => 'LONGTEXT NOT NULL'],
            'when'         => ['mysql' => 'INT(10) UNSIGNED NOT NULL INDEX'],
        ]]);

        parent::__construct($Database, $Model);

        $this->build();
        
    }
    
    public function add(string $name, $mixed, int $whenTimeunix = 0) {
        if($whenTimeunix == 0) $whenTimeunix = time();
        parent::add([
            'event'        => $name,
            'type'         => 'callback',
            'callback'     => $mixed,
            'when'         => $whenTimeunix,
        ]);
    }
    
    public function run() {
        $now = time();
        $queue = $this->select("SELECT * FROM `$this->tabela` WHERE `when` < $now LIMIT 10");
        print_r($queue);
    }
    
}
