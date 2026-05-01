<?php

use RBFrameworks\Core\Database\Traits\TableOperations;

class TableOperationsTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
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
