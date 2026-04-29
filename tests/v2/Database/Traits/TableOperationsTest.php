<?php

use RBFrameworks\Core\Database\Traits\TableOperations;

class TableOperationsTest extends \PHPUnit\Framework\TestCase
{
    public function testTableExistsExists()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'table_exists'));
    }

    public function testDropTableExists()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'drop_table'));
    }

    public function testCreateTableExists()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'createTable'));
    }

    public function testFieldExistsExists()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'field_exists'));
    }

    public function testBuildExists()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'build'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }

    public function testDropTable()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'dropTable'));
    }

    public function testTruncateTable()
    {
        $trait = $this->getObjectForTrait(TableOperations::class);
        $this->assertTrue(method_exists($trait, 'truncateTable'));
    }
        
}