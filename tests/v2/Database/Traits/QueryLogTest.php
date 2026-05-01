<?php

use RBFrameworks\Core\Database\Traits\QueryLog;

class QueryLogTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Database tests require database connection');
    }

    public function testClearLog()
    {
        $trait = $this->getObjectForTrait(QueryLog::class);
        $this->assertTrue(method_exists($trait, 'clearLog'));
    }

    public function testGetLastQuery()
    {
        $trait = $this->getObjectForTrait(QueryLog::class);
        $this->assertTrue(method_exists($trait, 'getLastQuery'));
    }
}
