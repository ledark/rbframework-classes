<?php

namespace RBFrameworks\Core\Tests;

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Database;
use RBFrameworks\Core\Utils\Arrays as ArraysDatabase;
use RBFrameworks\Core\Database\Legacy\Model as LegacyModel;

/**
 * @group Database
 */
class DBCase extends CommonCase {

    protected const CORE_TESTS_WRITELOCKER = false;

    protected function setUp() {
        parent::setUp();
        if(self::CORE_TESTS_WRITELOCKER) {
            file_put_contents('testing.lock', 'LAST TESTING RUNNING:'.date('Y-m-d H:i:s'), FILE_APPEND);
        }
    }

    /**
     * @doesNotPerformAssertions
     */    
    public function testAlgo() {
        
    }
    
    public function assertTableModel(string $tablename, array $model){
              
        //CreateInstance
        $database = new Database($tablename);

        //CreateQuery
        $query = "SELECT ".ArraysDatabase::extractFields($model)." FROM ".$database->getTabela(). " LIMIT 1";
        $this->assertIsString($query);

        

        try {

            $result = $database->queryFirstRow($query);

        } catch (\Exception $e) {

            $database->modelObject = new LegacyModel($model);

            //echo $database->getQueryOperation_CreateTable('INNODB');

            $database->createTable();

            //$database->build();
            $this->assertEquals('', $database->getTabela(), $e->getMessage());

        }

    }     
    
    public function assertTableExists(string $tablename){

        $database = new Database($tablename);

        $this->assertTrue( $database->table_exists() );
        
    } 
    
    protected function tearDown(): void {
        if (self::CORE_TESTS_WRITELOCKER) {
            \unlink('testing.lock');
        }
        parent::tearDown();
    }

    //QueryDatabaseUtils
    public function query(string $query):array {
        $database = new Database("");
        return $database->query($query);
    }
    public function queryFirstRow(string $query):array {
        $database = new Database("");
        return $database->queryFirstRow($query);
    }    

}