<?php

namespace Tests\Unit\Browser;

use PhpUnit\Framework\TestCase;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Cache;
use RBFrameworks\Core\Database\Selector;

class MultiDatabaseTest extends TestCase {

    public function testMultipleConections() {

        $itsfake = [
            'server'    => 'localhost',
            'login'     => 'user',
            'senha'     => 'mypass',
            'database'  => 'mydatabase',
            'prefixo'   => 'prefx_',
            'logs'      => 'logAll', //logAll logErrors logSuccess
            'type'      => 'mysql',    
        ];

        try {

            $result = Cache::stored(function() use ($itsfake){
                return Selector::select($itsfake, Config::get('database'), $itsfake);
            });

        } catch (\PDOException $e) {
            $this->fail($e->getMessage());
        }


        $this->assertEquals($result['database'], 'bermejo_gametest');

    }

}
