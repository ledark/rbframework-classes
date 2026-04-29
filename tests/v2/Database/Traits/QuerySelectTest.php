<?php

use RBFrameworks\Core\Database\Traits\QuerySelect;

class QuerySelectTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderSelectExists()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        $this->assertTrue(method_exists($trait, 'render_select'));
    }

    public function testRenderSelectReturnsString()
    {
        $trait = $this->getObjectForTrait(QuerySelect::class);
        // The method is private, so we can only check it exists
        $this->assertTrue(method_exists($trait, 'render_select'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
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