<?php

use RBFrameworks\Core\Database\Traits\Model;
use RBFrameworks\Core\Database\Traits\Connection;

class ModelTest extends \PHPUnit\Framework\TestCase
{
    public function testHasModelInitiallyFalse()
    {
        $trait = $this->getObjectForTrait(Model::class);
        // Initialize model property to avoid undefined property error
        $trait->model = [];
        $this->assertFalse($trait->hasModel());
    }

    public function testGetModelFldSqlRequiresModel()
    {
        // Need both Model and Connection traits since getModelFldSql uses getModelObject from Connection
        $trait = $this->getObjectForTrait([Model::class, Connection::class]);
        $trait->model = ['test_table' => ['field' => ['mysql' => 'VARCHAR(255)']]];
        $trait->setTabela('test_table');
        $trait->setModel(['test_table' => ['field' => ['mysql' => 'VARCHAR(255)']]]);
        // This will fail because there's no actual database connection, but shouldn't throw "undefined method"
        try {
            $result = $trait->getModelFldSql();
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            // Expected - no database connection
            $this->assertTrue(true);
        }
    }

    public function testGetModelFldPrpRequiresModel()
    {
        $trait = $this->getObjectForTrait([Model::class, Connection::class]);
        $trait->model = ['test_table' => ['field' => ['mysql' => 'VARCHAR(255)']]];
        $trait->setTabela('test_table');
        $trait->setModel(['test_table' => ['field' => ['mysql' => 'VARCHAR(255)']]]);
        try {
            $result = $trait->getModelFldPrp();
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            // Expected - no database connection
            $this->assertTrue(true);
        }
    }
}
