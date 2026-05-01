<?php

use RBFrameworks\Core\Database\Traits\QuerySelect;

class QuerySelectTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
    }

    public function testSelectAll()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        $this->assertTrue(method_exists($trait, 'selectAll'));
    }

    public function testSelectRow()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        $this->assertTrue(method_exists($trait, 'selectRow'));
    }

    public function testSelectCol()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        $this->assertTrue(method_exists($trait, 'selectCol'));
    }

    public function testSelectOne()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        $this->assertTrue(method_exists($trait, 'selectOne'));
    }
}
