<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Database\Form;
use Medoo\Medoo;
use RBFrameworks\Core\Database\Model\UserDadosMock;

//include(__DIR__.'/../Samples/Model/UserDados.php');

class FormTest extends TestCase
{

    public function testModel() {

        $Model = new UserDadosMock();
        $Form = new Form($Model->getModel());

       // $Form->generate();

        $this->assertDirectoryExists($Form->tmplDir);

    }    

    /*tests\Samples\Model\UserDados.php
    public function testModel()
    {

        $database = new Medoo([
            'type' => 'sqlite',
            'database' => __DIR__.'/../../rbframework.sqlite',
            'testMode' => false,
        ]);

        $res = $database->query("SELECT * FROM `framework`")->fetchAll();;
        $this->assertGreaterThan(5, count($res));
    
    }
    */

}