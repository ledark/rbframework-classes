<?php

use RBFrameworks\Core\Database\Traits\QueryLog;

class QueryLogTest extends \PHPUnit\Framework\TestCase
{
    public function testSetNameExists()
    {
        $trait = $this->getObjectForTrait(QueryLog::class);
        $this->assertTrue(method_exists($trait, 'setName'));
    }

    public function testWriteLogExists()
    {
        $trait = $this->getObjectForTrait(QueryLog::class);
        $this->assertTrue(method_exists($trait, 'writeLog'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
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