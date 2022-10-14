<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;
use RBFrameworks\Core\Database\Query;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Database\DatabaseFacade;

class DatabaseFacadeTest extends TestCase
{
    public function testQuerys() {
        

        $this->assertEquals(DatabaseFacade::getPrefixo(), Config::get('database.prefixo'));

        
    }
}