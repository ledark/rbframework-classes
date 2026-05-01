<?php

use RBFrameworks\Core\Database\Traits\QueryInsert;

class QueryInsertTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
    }

    public function testRenderInsertExists()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'render_insert'));
    }

    public function testInsertBatch()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'insertBatch'));
    }

    public function testLastInsertId()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'lastInsertId'));
    }
}
