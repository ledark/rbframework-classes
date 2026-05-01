<?php

use RBFrameworks\Core\Database\Traits\QueryUpdate;

class QueryUpdateTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
    }

    public function testUpdateBatch()
    {
        $trait = $this->getObjectForTrait(QueryUpdate::class);
        $this->assertTrue(method_exists($trait, 'updateBatch'));
    }
}
