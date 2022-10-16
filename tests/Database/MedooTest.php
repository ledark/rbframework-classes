<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Config;
use Medoo\Medoo;

class MedooTest extends TestCase
{

    public function testBasic()
    {

        $database = new Medoo([
            'type' => 'sqlite',
            'database' => __DIR__.'/../../rbframework.sqlite',
            'testMode' => false,
        ]);

        $res = $database->query("SELECT * FROM `framework`")->fetchAll();;
        $this->assertGreaterThan(5, count($res));
    
    }

}