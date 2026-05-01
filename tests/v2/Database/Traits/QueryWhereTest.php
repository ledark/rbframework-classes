<?php

use RBFrameworks\Core\Database\Traits\QueryWhere;

class QueryWhereTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
    }

    public function testWhere()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'where'));
    }

    public function testOrWhere()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'orWhere'));
    }

    public function testWhereIn()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'whereIn'));
    }
}
