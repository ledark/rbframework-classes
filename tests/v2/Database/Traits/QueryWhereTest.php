<?php

use RBFrameworks\Core\Database\Traits\QueryWhere;

class QueryWhereTest extends \PHPUnit\Framework\TestCase
{
    public function testSetWhereExists()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'setWhere'));
    }

    public function testSetWhereAndExists()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'setWhereAnd'));
    }

    public function testSetWhereOrExists()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'setWhereOr'));
    }

    public function testSetWhereInExists()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'setWhereIn'));
    }

    public function testSetGroupExists()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'setGroup'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }


    public function testWhere()
    {
        $trait = $this->getObjectForTrait(QueryWhere::class);
        $this->assertTrue(method_exists($trait, 'where'));
    }    
}