<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;
use RBFrameworks\Core\Database\Query;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Database\DatabaseFacade;

class DatabaseFacadeTest extends TestCase
{
    public function testQuerys() {
        
        $this->assertNotNull(DatabaseFacade::getPrefixo());
        $this->assertEquals(DatabaseFacade::getPrefixo(), Config::get('database.prefixo'));
        
        DatabaseFacade::setPrefixo('gametest160521_');
        $this->assertEquals(DatabaseFacade::getPrefixo(), 'gametest160521_');

        /*
        $x = DatabaseFacade::query("SELECT * FROM gametest160521_gameareas");
        print_r($x);
        */
        
    }
}