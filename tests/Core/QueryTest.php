<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;
use RBFrameworks\Core\Database\Query;
use RBFrameworks\Core\Config;

class QueryTest extends TestCase
{
    public function testQuerys() {

        $Query = new Query();

        $Query->writeLog("teste");
        $this->assertEquals($Query->getPrefixo(), Config::get('database.prefixo'));
        //$this->assertCount(26, $Query->alfabeto);
        
    }

    public function testSelect() {

        $Query = new Query();
        $this->assertEquals($Query->renderRaw(), 'SELECT FROM ``');

        $Query->setField('campo1');
        $Query->setField('campo2');

        $this->assertEquals($Query->renderRaw(), 'SELECT `campo1` , `campo2` FROM ``');

        $Query->setField('campo3', 'AAAA');
        $Query->setFrom('tabela1');
        $this->assertEquals($Query->renderRaw(), 'SELECT `campo1` , `campo2` , AAAA AS `campo3` FROM tabela1');

    }


    /*
    public function testHello()
    {
        $_GET['name'] = 'Fabien';

        ob_start();
        include 'index.php';
        $content = ob_get_clean();

        $this->assertEquals('Hello Fabien', $content);
    }
    */
}