<?php

use RBFrameworks\Core\Database\Traits\TableQueryOperations;

class TableQueryOperationsTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
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
