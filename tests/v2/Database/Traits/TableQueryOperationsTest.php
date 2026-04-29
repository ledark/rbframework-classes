<?php

use RBFrameworks\Core\Database\Traits\TableQueryOperations;

class TableQueryOperationsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQueryOperationCreateTableExists()
    {
        $trait = $this->getObjectForTrait(TableQueryOperations::class);
        $this->assertTrue(method_exists($trait, 'getQueryOperation_CreateTable'));
    }

    public function testGetQueryOperationAlterTableExists()
    {
        $trait = $this->getObjectForTrait(TableQueryOperations::class);
        $this->assertTrue(method_exists($trait, 'getQueryOperation_AlterTable'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }

    public function testGetCount()
    {
        $trait = $this->getObjectForTrait(TableQueryOperations::class);
        $this->assertTrue(method_exists($trait, 'getCount'));
    }

    public function testGetFirst()
    {
        $trait = $this->getObjectForTrait(TableQueryOperations::class);
        $this->assertTrue(method_exists($trait, 'getFirst'));
    }
        
}
