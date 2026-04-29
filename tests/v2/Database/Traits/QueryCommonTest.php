<?php

use RBFrameworks\Core\Database\Traits\QueryCommon;

class QueryCommonTest extends \PHPUnit\Framework\TestCase
{
    public function testSetField()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'setField'));
    }

    public function testSetFields()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'setFields'));
    }

    public function testSetFrom()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'setFrom'));
    }

    public function testSetLimit()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'setLimit'));
    }

    public function testSetOrder()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'setOrder'));
    }

    public function testUseTables()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'useTables'));
    }

    public function testClear()
    {
        $trait = $this->getObjectForTrait(QueryCommon::class);
        $this->assertTrue(method_exists($trait, 'clear'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }
}
