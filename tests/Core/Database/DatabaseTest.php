<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Types\PropProps;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Types\Model;

require_once(__DIR__.'/Model/UserDadosMock.php');

class DatabaseTest extends TestCase {

    /*
    'cod' => [
        'mysql' => 'int(10) unsigned NOT NULL PRIMARY auto_increment',
        'label' => 'Cod',
        'default' => '',
        'asKey' => true,
    ],
    'id_vended' => [
        'mysql' => 'varchar(255) NOT NULL ',
        'label' => 'Id_vended',
        'default' => '',
    ],
    */    
    private static function getModelWithProps():array {        
        return RBFrameworks\Core\Database\Model\UserDadosMock::getModel();
    }

    private static function getModelWithTableAndProps():array {        
        return ['mytablename' => RBFrameworks\Core\Database\Model\UserDadosMock::getModel()];
    }

    private static function getModelWithPropMysql():array {        
        return RBFrameworks\Core\Database\Model\UserDadosMock::getModelWithFieldMysql();
    }

    private static function getModelWithTableAndPropMysql():array {        
        return ['mytablename' => RBFrameworks\Core\Database\Model\UserDadosMock::getModelWithFieldMysql()];
    }

    public function testModelTypes() {
        
        //getModelWithProps [props]
        $this->assertArrayHasKey('cod', $this->getModelWithProps());
        $this->assertArrayHasKey('id_vended', $this->getModelWithProps());
        $this->assertArrayHasKey('status', $this->getModelWithProps());
        $this->assertArrayHasKey('login', $this->getModelWithProps());
        $this->assertCount(4, $this->getModelWithProps()['cod']);
        $this->assertArrayHasKey('mysql', $this->getModelWithProps()['cod']);
        $this->assertArrayHasKey('label', $this->getModelWithProps()['cod']);
        $this->assertArrayHasKey('default', $this->getModelWithProps()['cod']);
        $this->assertArrayHasKey('mysql', $this->getModelWithProps()['status']);
        $this->assertArrayHasKey('label', $this->getModelWithProps()['status']);
        $this->assertArrayHasKey('default', $this->getModelWithProps()['status']);

        //getModelWithTableProps [table => props]
        $this->assertCount(1, $this->getModelWithTableAndProps());
        $this->assertArrayHasKey('cod', $this->getModelWithTableAndProps()['mytablename']);
        $this->assertArrayHasKey('id_vended', $this->getModelWithTableAndProps()['mytablename']);
        $this->assertArrayHasKey('mysql', $this->getModelWithTableAndProps()['mytablename']['id_vended']);
        
        //getModelWithPropMysql [field => mysqlquery]
        $this->assertArrayHasKey('cod', $this->getModelWithPropMysql());
        $this->assertArrayHasKey('id_vended', $this->getModelWithPropMysql());
        $this->assertArrayHasKey('status', $this->getModelWithPropMysql());
        $this->assertArrayNotHasKey('mysql', $this->getModelWithPropMysql());
        $this->assertIsString($this->getModelWithPropMysql()['id_vended']);
        $this->assertEquals($this->getModelWithPropMysql()['id_vended'], 'varchar(255) NOT NULL ');
        
        //getModelWithPropMysql [table => [field => mysqlquery], [field => mysqlquery]]
        $this->assertCount(1, $this->getModelWithTableAndPropMysql());
        $this->assertArrayHasKey('cod', $this->getModelWithTableAndPropMysql()['mytablename']);
        $this->assertArrayHasKey('id_vended', $this->getModelWithTableAndPropMysql()['mytablename']);
        $this->assertArrayHasKey('status', $this->getModelWithTableAndPropMysql()['mytablename']);
        $this->assertArrayNotHasKey('mysql', $this->getModelWithTableAndPropMysql()['mytablename']);
        $this->assertIsString($this->getModelWithTableAndPropMysql()['mytablename']['id_vended']);
        $this->assertEquals($this->getModelWithTableAndPropMysql()['mytablename']['id_vended'], 'varchar(255) NOT NULL ');        
    }

    public function testModelCheck() {
        $this->assertEquals('[Fld->Prp]', (new Model($this->getModelWithProps()))->getType());
        $this->assertEquals('[Tab->Fld->Prp]', (new Model($this->getModelWithTableAndProps()))->getType());
        $this->assertEquals('[Fld->Sql]', (new Model($this->getModelWithPropMysql()))->getType());
        $this->assertEquals('[Tab->Fld->Sql]', (new Model($this->getModelWithTableAndPropMysql()))->getType());
    }
    
    public function testConnection() {
        $model = RBFrameworks\Core\Database\Model\UserDadosMock::getModel();
        $model = new Model($model);
        //$modelType = (new Model($model))->getType();
        $conn = new Database('untitled_table', $model->getFldSql());

        $tables = $conn->query("SHOW TABLES");
        $this->assertIsArray($tables);

        $conn->build();

        print_r($tables);
    }
    
}